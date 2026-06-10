from nltk.sentiment.vader import SentimentIntensityAnalyzer
from sklearn.feature_extraction.text import TfidfVectorizer
import pandas as pd
from django.db.models import Count, F
from django.utils import timezone
from datetime import timedelta
from django.contrib import messages
from .models import Review, SuspiciousIP

# Fake review detection keywords (expanded list)
FAKE_KEYWORDS = [
    'buy now', 'free', 'click here', 'limited offer', 'guaranteed', 'act now',
    '100% satisfaction', 'don’t miss out', 'exclusive deal', 'earn money',
    'miracle', 'instant results', 'cheap', 'order now', 'win big', 'no risk',
    'only today', 'best price', 'money-back guarantee', 'as seen on', 'special promotion',
    'get rich', 'fast delivery', 'limited time', 'hot deal', 'super cheap', 'get it now'
]

# Analyze sentiment using VADER
def analyze_sentiment_vader(text):
    sia = SentimentIntensityAnalyzer()
    score = sia.polarity_scores(text)
    
    compound = score['compound']
    if compound >= 0.05:
        return 'positive'
    elif compound <= -0.05:
        return 'negative'
    return 'neutral'

# Extract top TF-IDF keywords
def extract_top_keywords(reviews):
    tfidf = TfidfVectorizer(stop_words='english')
    tfidf_matrix = tfidf.fit_transform(reviews)
    last_review_vec = tfidf_matrix[-1]
    feature_names = tfidf.get_feature_names_out()
    df = pd.DataFrame(last_review_vec.toarray(), columns=feature_names)
    top_keywords = df.T.sort_values(by=0, ascending=False).head(5).index.tolist()
    return top_keywords

# Detect fake review based on keywords
def detect_fake_review(comment):
    comment = comment.lower()
    return any(keyword in comment for keyword in FAKE_KEYWORDS)

# Capture IP Address (supports Nginx/Apache)
def get_client_ip(request):
    x_forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR')
    if x_forwarded_for:
        ip = x_forwarded_for.split(',')[0]
    else:
        ip = request.META.get('REMOTE_ADDR')
    return ip

def detect_repeated_fake_ip(ip, threshold=3):
    count = Review.objects.filter(ip_address=ip, is_fake=True).count()
    return count >= threshold

def detect_frequent_reviews(ip, minutes=10, limit=3):
    time_limit = timezone.now() - timedelta(minutes=minutes)
    count = Review.objects.filter(
        ip_address=ip,
        created_at__gte=time_limit
    ).count()
    return count >= limit

def notify_admin(ip):
    """Track suspicious IP and increment fake review count"""
    # Get current fake review count for this IP
    fake_count = Review.objects.filter(ip_address=ip, is_fake=True).count()
    
    # Update or create SuspiciousIP entry
    suspicious_ip, created = SuspiciousIP.objects.get_or_create(
        ip_address=ip,
        defaults={'fake_review_count': fake_count}
    )
    
    # If already exists, update the count
    if not created:
        suspicious_ip.fake_review_count = fake_count
        suspicious_ip.save()
    
    # ACTUAL NOTIFICATION - Add this!
    print(f"⚠️ ALERT: Suspicious IP {ip} has {fake_count} fake reviews")
    
