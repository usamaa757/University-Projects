from django.shortcuts import render
import joblib
import numpy as np
import re
import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences

# Download required NLTK assets once
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Load models & vectorizer/tokenizer
log_model = joblib.load('models/logreg_model.pkl')
vectorizer = joblib.load('models/tfidf_vectorizer.pkl')
lstm_model = load_model('models/lstm_model.keras')
tokenizer = joblib.load('models/tokenizer.pkl')

MAX_LEN = 200

def index(request):
    context = {}
    # after model prediction or wherever appropriate
    context['training_plot'] = '/media/lstm_training_history.png'
    return render(request, 'index.html', context)


def clean_text(text):
    text = text.lower()
    text = re.sub(r'<[^>]+>', '', text)
    text = re.sub(r'[^\w\s]', '', text)
    text = re.sub(r'\d+', '', text)
    lemmatizer = WordNetLemmatizer()
    stop_words = set(stopwords.words('english'))
    words = text.split()
    cleaned = [lemmatizer.lemmatize(word) for word in words if word not in stop_words]
    return ' '.join(cleaned)

# def analyze_review(request):
#     sentiment = None
#     cleaned = ""

#     if request.method == 'POST':
#         review = request.POST.get('review')
#         model_choice = request.POST.get('model_choice')
#         cleaned = clean_text(review)

#         if model_choice == 'logreg':
#             vector = vectorizer.transform([cleaned])
#             pred = log_model.predict(vector)[0]
#         else:
#             seq = tokenizer.texts_to_sequences([cleaned])
#             padded = pad_sequences(seq, maxlen=MAX_LEN, padding='post', truncating='post')
#             pred_prob = lstm_model.predict(padded)[0][0]
#             pred = (lstm_model.predict(padded)[0][0] > 0.5).astype(int)

#         sentiment = "Positive" if pred == 1 else "Negative"

#     return render(request, 'index.html', {
#         'sentiment': sentiment,
#         'cleaned_review': cleaned  
#     })
def analyze_review(request):
    sentiment = None
    cleaned = ""
    pred_prob_lstm = None
    pred_prob_logreg = None
    sentiment_lstm = None
    sentiment_logreg = None

    if request.method == 'POST':
        review = request.POST.get('review')
        model_choice = request.POST.get('model_choice')
        cleaned = clean_text(review)

        # Logistic Regression prediction
        vector = vectorizer.transform([cleaned])
        pred_logreg_prob = log_model.predict_proba(vector)[0][1]
        pred_logreg = log_model.predict(vector)[0]
        sentiment_logreg = "Positive" if pred_logreg == 1 else "Negative"
        pred_prob_logreg = pred_logreg_prob

        # LSTM prediction
        seq = tokenizer.texts_to_sequences([cleaned])
        padded = pad_sequences(seq, maxlen=MAX_LEN, padding='post', truncating='post')
        pred_lstm_prob = lstm_model.predict(padded)[0][0]
        pred_lstm = (pred_lstm_prob > 0.5).astype(int)
        sentiment_lstm = "Positive" if pred_lstm == 1 else "Negative"
        pred_prob_lstm = pred_lstm_prob

        # Final choice model result
        sentiment = sentiment_lstm if model_choice == 'lstm' else sentiment_logreg

    return render(request, 'index.html', {
        'sentiment': sentiment,
        'cleaned_review': cleaned,
        'pred_prob_lstm': pred_prob_lstm,
        'pred_prob_logreg': pred_prob_logreg,
        'sentiment_lstm': sentiment_lstm,
        'sentiment_logreg': sentiment_logreg,
    })
