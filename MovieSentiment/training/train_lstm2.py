import pandas as pd
import numpy as np
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Embedding, LSTM, Dense, Dropout, Bidirectional
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.callbacks import EarlyStopping
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report
import pickle

# Constants
MAX_VOCAB = 10000
MAX_LEN = 200
EMBEDDING_DIM = 64
EPOCHS = 20

# Load dataset
df = pd.read_csv("training/movie_reviews.csv")
df.dropna(inplace=True)

# Prepare data
texts = df["review"].astype(str).values
labels = df["sentiment"].map({"positive": 1, "negative": 0}).values


# Tokenization
tokenizer = Tokenizer(num_words=MAX_VOCAB, oov_token="<OOV>")
tokenizer.fit_on_texts(texts)
sequences = tokenizer.texts_to_sequences(texts)
padded = pad_sequences(sequences, maxlen=MAX_LEN, padding="post", truncating="post")

# Split data
X_train, X_test, y_train, y_test = train_test_split(padded, labels, test_size=0.2, random_state=42)

# Build model
model = Sequential([
    Embedding(MAX_VOCAB, EMBEDDING_DIM, input_length=MAX_LEN),
    Bidirectional(LSTM(128, return_sequences=True)),
    Bidirectional(LSTM(64)),
    Dropout(0.3),
    Dense(64, activation="relu"),
    Dropout(0.3),
    Dense(1, activation="sigmoid")
])

model.compile(loss="binary_crossentropy", optimizer="adam", metrics=["accuracy"])
model.summary()

# Early stopping
early_stop = EarlyStopping(monitor='val_loss', patience=3, restore_best_weights=True)

# Train model
history = model.fit(
    X_train, y_train,
    epochs=EPOCHS,
    validation_split=0.2,
    callbacks=[early_stop],
    verbose=1
)

# Evaluate
loss, accuracy = model.evaluate(X_test, y_test, verbose=1)
print(f"\n✅ Test Accuracy: {accuracy:.4f}")

# Predictions
y_pred = (model.predict(X_test) > 0.5).astype("int32")
print("\n📊 Classification Report:\n", classification_report(y_test, y_pred))

# Save model and tokenizer
model.save("models/lstm_model.h5")
with open("models/tokenizer.pkl", "wb") as f:
    pickle.dump(tokenizer, f)
