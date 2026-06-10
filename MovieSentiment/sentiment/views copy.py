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
lstm_model = load_model('models/lstm_model.h5')
tokenizer = joblib.load('models/tokenizer.pkl')

MAX_LEN = 200

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

def analyze_review(request):
    sentiment = None

    if request.method == 'POST':
        review = request.POST.get('review')
        model_choice = request.POST.get('model_choice')
        cleaned = clean_text(review)
        print("🧹 Cleaned input:", cleaned)


        if model_choice == 'logreg':
            vector = vectorizer.transform([cleaned])
            pred = log_model.predict(vector)[0]
        else:
            seq = tokenizer.texts_to_sequences([cleaned])
            padded = pad_sequences(seq, maxlen=MAX_LEN, padding='post', truncating='post')
            pred = (lstm_model.predict(padded)[0][0] > 0.5).astype(int)
            raw_pred = lstm_model.predict(padded)
            print("🧠 Tokenizer word index sample:", dict(list(tokenizer.word_index.items())[:10]))
            print("🧠 Word index for 'fantastic':", tokenizer.word_index.get("fantastic", "❌ Not found"))
            print("🧠 Word index for 'nice':", tokenizer.word_index.get("nice", "❌ Not found"))
            print("🧠 Word index for 'movie':", tokenizer.word_index.get("movie", "❌ Not found"))


        sentiment = "Positive" if pred == 1 else "Negative"

    return render(request, 'index.html', {'sentiment': sentiment})
