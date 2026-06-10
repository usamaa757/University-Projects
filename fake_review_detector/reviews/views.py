from django.shortcuts import render, redirect, get_object_or_404
from django.contrib import messages
from django.contrib.auth.decorators import login_required, user_passes_test
from django.db.models import Count, Q
from .forms import ReviewForm
from .models import Review, SuspiciousIP
from products.models import Product

from .utils import (
    get_client_ip,
    analyze_sentiment_vader,
    detect_fake_review,
    detect_repeated_fake_ip,
    detect_frequent_reviews,
    notify_admin
)


from nltk.sentiment.vader import SentimentIntensityAnalyzer
import nltk

# Download necessary NLTK resources
nltk.download('vader_lexicon')
nltk.download('punkt')

# Superuser check
def is_superuser(user):
    return user.is_authenticated and user.is_superuser

def is_user(user):
    return user.is_authenticated and user.is_user

# Add Review
@login_required
def add_review(request, pk):
    product = get_object_or_404(Product, pk=pk)
    reviews = product.reviews.all()

    
    if request.method == 'POST':
        form = ReviewForm(request.POST)
        if form.is_valid():
            review = form.save(commit=False)
            review.product = product
            review.user = request.user
            review.ip_address = get_client_ip(request)

            # Sentiment Analysis
            review.sentiment = analyze_sentiment_vader(review.comment)

            # Keyword-based fake detection
            review.is_fake = detect_fake_review(review.comment)

            review.save()

            # -------------------------------
            # IP BASED FAKE REVIEW DETECTION
            # -------------------------------
            if detect_repeated_fake_ip(review.ip_address):
                Review.objects.filter(
                    ip_address=review.ip_address
                ).update(is_fake=True)
                notify_admin(review.ip_address)

            # -------------------------------
            # TIME BASED DETECTION
            # -------------------------------
            if detect_frequent_reviews(review.ip_address):
                Review.objects.filter(
                    ip_address=review.ip_address
                ).update(is_fake=True)
                notify_admin(review.ip_address)

            messages.success(request, "Review submitted.")
            return redirect('add_review', pk=product.pk)
    else:
        form = ReviewForm()

    return render(request, 'add_review.html', {
        'product': product,
        'form': form,
        'reviews': reviews
    })

@login_required
@user_passes_test(is_superuser)
def suspicious_ips(request):
    ips = SuspiciousIP.objects.all().order_by('-detected_at')
    return render(request, 'suspicious_ips.html', {'ips': ips})

# Delete Review
@login_required
@user_passes_test(is_superuser)
def delete_review(request, pk):
    review = get_object_or_404(Review, pk=pk)
    review.delete()
    messages.success(request, 'Review deleted.')
    return redirect('fake_reviews')


# Analyze sentiment using Vader

def analyze_sentiment(text):
    sia = SentimentIntensityAnalyzer()
    sentiment_scores = sia.polarity_scores(text)
    compound = sentiment_scores['compound']
    
    if compound >= 0.05:
        return 'positive'
    elif compound <= -0.05:
        return 'negative'
    else:
        return 'neutral'

# Show Fake Reviews
@login_required
def fake_reviews(request):
    fake_reviews = Review.objects.filter(is_fake=True)
    return render(request, 'fake_reviews.html', {'fake_reviews': fake_reviews})

# Product Ranking
@user_passes_test(is_superuser)
def product_ranking(request):
    products = Product.objects.annotate(
        total_reviews=Count('reviews'),
        positive=Count('reviews', filter=Q(reviews__sentiment='positive')),
        negative=Count('reviews', filter=Q(reviews__sentiment='negative')),
        neutral=Count('reviews', filter=Q(reviews__sentiment='neutral'))
    ).order_by('-positive')

    return render(request, 'product_ranking.html', {'products': products})



@login_required
def review_analysis(request):
    sia = SentimentIntensityAnalyzer()
    
    # Check if user is admin/superuser
    if request.user.is_superuser:
        # Admin sees all reviews
        all_reviews = Review.objects.all().order_by('-created_at')
    else:
        # Regular user sees only their own reviews
        all_reviews = Review.objects.filter(user=request.user).order_by('-created_at')
    
    # Add actual sentiment score to each review
    for review in all_reviews:
        # Get actual compound score from VADER (-1 to +1)
        scores = sia.polarity_scores(review.comment)
        review.sentiment_score = round(scores['compound'], 3)  # Actual score e.g., 0.85, -0.42, 0.00
    
    # Filter by sentiment
    positive_reviews = [r for r in all_reviews if r.sentiment == 'positive']
    neutral_reviews = [r for r in all_reviews if r.sentiment == 'neutral']
    negative_reviews = [r for r in all_reviews if r.sentiment == 'negative']
    
    context = {
        'all_reviews': all_reviews,
        'positive_reviews': positive_reviews,
        'neutral_reviews': neutral_reviews,
        'negative_reviews': negative_reviews,
        'is_admin': request.user.is_superuser,
    }
    return render(request, 'review_analysis.html', context)