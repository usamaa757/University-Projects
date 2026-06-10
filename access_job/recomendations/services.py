import re

from accounts.models import Profile
from applications.models import Application, Bookmark
from jobs.models import Job

from .models import Recommendation


def _tokenize(text):
    return set(re.findall(r"[a-zA-Z0-9]+", (text or "").lower()))


def _content_score(profile, job):
    user_tokens = _tokenize(profile.skills) | _tokenize(profile.education) | _tokenize(profile.experience)
    job_tokens = _tokenize(job.title) | _tokenize(job.description) | _tokenize(job.category)
    if not user_tokens or not job_tokens:
        return 0.0
    overlap = len(user_tokens & job_tokens)
    return overlap / max(len(user_tokens), 1)


def _collaborative_score(user, job):
    total_applications = max(Application.objects.count(), 1)
    total_bookmarks = max(Bookmark.objects.count(), 1)
    job_applications = Application.objects.filter(job=job).count()
    job_bookmarks = Bookmark.objects.filter(job=job).count()
    popularity_score = ((job_applications / total_applications) + (job_bookmarks / total_bookmarks)) / 2

    user_categories = set(
        Application.objects.filter(user=user).values_list("job__category", flat=True)
    ) | set(Bookmark.objects.filter(user=user).values_list("job__category", flat=True))
    category_boost = 0.2 if job.category in user_categories and job.category else 0.0

    return min(popularity_score + category_boost, 1.0)


def generate_user_recommendations(user, top_n=20):
    profile, _ = Profile.objects.get_or_create(user=user)
    jobs = Job.objects.filter(status="open")

    scored = []
    for job in jobs:
        content = _content_score(profile, job)
        collaborative = _collaborative_score(user, job)
        hybrid = (0.7 * content) + (0.3 * collaborative)
        scored.append((job, content, collaborative, hybrid))

    scored.sort(key=lambda item: item[3], reverse=True)
    top_scored = scored[:top_n]

    for job, content, collaborative, hybrid in top_scored:
        Recommendation.objects.update_or_create(
            user=user,
            job=job,
            model_type="content",
            defaults={
                "score": round(content, 4),
                "reason": "Matched profile skills and job description.",
            },
        )
        Recommendation.objects.update_or_create(
            user=user,
            job=job,
            model_type="collaborative",
            defaults={
                "score": round(collaborative, 4),
                "reason": "Matched using user behavior and job popularity.",
            },
        )
        Recommendation.objects.update_or_create(
            user=user,
            job=job,
            model_type="hybrid",
            defaults={
                "score": round(hybrid, 4),
                "reason": "Hybrid score from content + collaborative signals.",
            },
        )

    return len(top_scored)
