import pandas as pd
import re
import nltk
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer

# Download NLTK resources (only needed once)
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Load dataset
df = pd.read_csv("training/movie_reviews.csv")

# Setup
lemmatizer = WordNetLemmatizer()
stop_words = set(stopwords.words('english'))

def clean_text(text):
    text = str(text).lower()
    text = re.sub(r'<.*?>', ' ', text)
    text = re.sub(r'[^a-z\s]', '', text)
    words = text.split()
    words = [lemmatizer.lemmatize(w) for w in words if w not in stop_words]
    return ' '.join(words)

# Apply cleaning
df['cleaned_review'] = df['review'].apply(clean_text)

# Normalize sentiment labels
df['sentiment'] = df['sentiment'].str.strip().str.lower()

# Save to new CSV
df.to_csv("cleaned_movie_reviews.csv", index=False)
print("✅ Cleaned dataset saved as 'cleaned_movie_reviews.csv'")
