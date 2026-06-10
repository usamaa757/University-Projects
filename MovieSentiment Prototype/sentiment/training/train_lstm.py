# train_lstm.py

import pandas as pd
import re
import nltk
import joblib
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Embedding, LSTM, Dense, Dropout, Bidirectional
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
from sklearn.model_selection import train_test_split

nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Config
DATA_PATH = 'training/movie_reviews.csv'
MODEL_PATH = 'models/lstm_model.keras'
TOKENIZER_PATH = 'models/tokenizer.pkl'
MAX_VOCAB = 10000
MAX_LEN = 200
EMBEDDING_DIM = 128
BATCH_SIZE = 64
EPOCHS = 10

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
df['review'] = df['review'].astype(str)
df['sentiment'] = df['sentiment'].str.lower()
df = df[df['sentiment'].isin(['positive', 'negative'])]
df['cleaned_review'] = df['review'].apply(clean_text)
df['label'] = df['sentiment'].map({'positive': 1, 'negative': 0})

# Tokenize
tokenizer = Tokenizer(num_words=MAX_VOCAB, oov_token='<OOV>')
tokenizer.fit_on_texts(df['cleaned_review'])
sequences = tokenizer.texts_to_sequences(df['cleaned_review'])
padded = pad_sequences(sequences, maxlen=MAX_LEN, padding='post', truncating='post')

X_train, _, y_train, _ = train_test_split(padded, df['label'].values, test_size=0.3, stratify=df['label'], random_state=42)

# LSTM Model
model = Sequential([
    Embedding(MAX_VOCAB, EMBEDDING_DIM, input_length=MAX_LEN),
    Bidirectional(LSTM(64, return_sequences=True)),
    Dropout(0.3),
    Bidirectional(LSTM(32)),
    Dropout(0.3),
    Dense(32, activation='relu'),
    Dropout(0.2),
    Dense(1, activation='sigmoid')
])

model.compile(loss='binary_crossentropy', optimizer='adam', metrics=['accuracy'])

callbacks = [EarlyStopping(monitor='val_loss', patience=3, restore_best_weights=True),
             ModelCheckpoint(MODEL_PATH, monitor='val_loss', save_best_only=True)]

# Train
model.fit(X_train, y_train, epochs=EPOCHS, batch_size=BATCH_SIZE, validation_split=0.1, callbacks=callbacks, verbose=1)

# Save
model.save(MODEL_PATH)
joblib.dump(tokenizer, TOKENIZER_PATH)
print("Prototype LSTM model + tokenizer saved.")
