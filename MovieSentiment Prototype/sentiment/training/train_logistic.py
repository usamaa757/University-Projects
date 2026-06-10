# logistic_model.py

import pandas as pd
import re
import nltk
import joblib
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split

nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Config
DATA_PATH = 'training/movie_reviews.csv'
MODEL_PATH = 'models/logreg_model.pkl'
VECTORIZER_PATH = 'models/tfidf_vectorizer.pkl'

def clean_text(text):
    text = text.lower()
    text = re.sub(r'<[^>]+>', '', text)
    text = re.sub(r'[^\w\s]', '', text)
    text = re.sub(r'\d+', '', text)
    lemmatizer = WordNetLemmatizer()
    stop_words = set(stopwords.words('english'))
    return ' '.join([lemmatizer.lemmatize(word) for word in text.split() if word not in stop_words])

# Load and clean data
df = pd.read_csv(DATA_PATH)
df.dropna(subset=['review', 'sentiment'], inplace=True)
df['cleaned_review'] = df['review'].apply(clean_text)
df['sentiment'] = df['sentiment'].str.lower()
df = df[df['sentiment'].isin(['positive', 'negative'])]
df['label'] = df['sentiment'].map({'positive': 1, 'negative': 0})

# TF-IDF + Train
vectorizer = TfidfVectorizer(ngram_range=(1, 2), max_features=5000)
X = vectorizer.fit_transform(df['cleaned_review'])
y = df['label'].values

X_train, _, y_train, _ = train_test_split(X, y, test_size=0.3, stratify=y, random_state=42)

model = LogisticRegression(max_iter=1000)
model.fit(X_train, y_train)

# Save model/vectorizer
joblib.dump(model, MODEL_PATH)
joblib.dump(vectorizer, VECTORIZER_PATH)
print("Prototype LR model + vectorizer saved.")
