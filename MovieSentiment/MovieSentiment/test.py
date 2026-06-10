# test_logreg.py

import joblib
import re
import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer

# Load model and vectorizer
model = joblib.load('models/logreg_model.pkl')
vectorizer = joblib.load('models/tfidf_vectorizer.pkl')

# Download NLTK assets if not already
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Text preprocessing
def clean_text(text):
    text = text.lower()
    text = re.sub(r'<[^>]+>', '', text)
    text = re.sub(r'[^\w\s]', '', text)
    text = re.sub(r'\d+', '', text)
    text = text.strip()

    lemmatizer = WordNetLemmatizer()
    stop_words = set(stopwords.words('english'))
    words = text.split()
    cleaned = [lemmatizer.lemmatize(word) for word in words if word not in stop_words]
    return ' '.join(cleaned)

# === TEST INPUT ===
test_review = input("Enter your movie review: ")
cleaned = clean_text(test_review)
vectorized = vectorizer.transform([cleaned])
prediction = model.predict(vectorized)[0]

sentiment = "Positive" if prediction == 1 else "Negative"
print(f"Predicted Sentiment: {sentiment}")
