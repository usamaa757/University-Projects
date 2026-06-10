# training/train_model.py

import pandas as pd
import numpy as np
import joblib
import re
import string
import nltk
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, confusion_matrix
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer

# Download necessary NLTK data
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# ----------- CONFIG ----------
DATA_PATH = 'training/movie_reviews.csv'     # <- You must have this CSV file
MODEL_PATH = 'models/logreg_model.pkl'
VECTORIZER_PATH = 'models/tfidf_vectorizer.pkl'
# -----------------------------

# 1. Text Cleaning Function
def clean_text(text):
    text = text.lower()
    text = re.sub(r'<[^>]+>', '', text)                    # Remove HTML
    text = re.sub(r'[^\w\s]', '', text)                    # Remove punctuation
    text = re.sub(r'\d+', '', text)                        # Remove digits
    text = text.strip()

    lemmatizer = WordNetLemmatizer()
    words = text.split()
    stop_words = set(stopwords.words('english'))
    cleaned = [lemmatizer.lemmatize(w) for w in words if w not in stop_words]
    return ' '.join(cleaned)

# 2. Load Dataset
df = pd.read_csv(DATA_PATH)

if 'review' not in df.columns or 'sentiment' not in df.columns:
    raise ValueError("CSV must contain 'review' and 'sentiment' columns.")

df.dropna(subset=['review', 'sentiment'], inplace=True)
df['cleaned_review'] = df['review'].apply(clean_text)

# 3. Encode labels
df['sentiment'] = df['sentiment'].str.lower()
label_map = {'positive': 1, 'negative': 0}
df = df[df['sentiment'].isin(label_map.keys())]  # remove unknown labels
df['label'] = df['sentiment'].map(label_map)

# 4. TF-IDF Vectorization
vectorizer = TfidfVectorizer(ngram_range=(1, 2), max_features=5000)
X = vectorizer.fit_transform(df['cleaned_review'])
y = df['label'].values

# 5. Train-Test Split
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.3, random_state=42, stratify=y
)

# 6. Train Model
model = LogisticRegression(max_iter=1000)
model.fit(X_train, y_train)

# 7. Evaluate
y_pred = model.predict(X_test)
print("Classification Report:\n", classification_report(y_test, y_pred))
print("Confusion Matrix:\n", confusion_matrix(y_test, y_pred))

# 8. Save Model and Vectorizer
joblib.dump(model, MODEL_PATH)
joblib.dump(vectorizer, VECTORIZER_PATH)

print(f"✅ Logistic Regression model saved to {MODEL_PATH}")
print(f"✅ TF-IDF vectorizer saved to {VECTORIZER_PATH}")
