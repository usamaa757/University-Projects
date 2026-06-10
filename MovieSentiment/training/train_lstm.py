import pandas as pd
import numpy as np
import re
import nltk
import joblib
from tensorflow.keras.preprocessing.text import Tokenizer
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Embedding, LSTM, Dense, Dropout, Bidirectional
from tensorflow.keras.callbacks import EarlyStopping, ModelCheckpoint
from tensorflow.keras.regularizers import l2
from sklearn.model_selection import train_test_split
from sklearn.utils.class_weight import compute_class_weight
from nltk.corpus import stopwords
from nltk.stem import WordNetLemmatizer
import matplotlib.pyplot as plt

# Download NLTK assets
nltk.download('stopwords')
nltk.download('wordnet')
nltk.download('omw-1.4')

# -------- CONFIG --------
DATA_PATH = 'training/movie_reviews.csv'
MODEL_PATH = 'models/lstm_model.keras'
TOKENIZER_PATH = 'models/tokenizer.pkl'
MAX_VOCAB = 10000
MAX_LEN = 200
EMBEDDING_DIM = 128
BATCH_SIZE = 64
EPOCHS = 15
# ------------------------

def clean_text(text):
    """Enhanced text cleaning function"""
    text = str(text).lower()
    text = re.sub(r'&lt;/?.*?&gt;', ' &lt;&gt; ', text)  # Handle HTML tags
    text = re.sub(r'[^\w\s]', ' ', text)  # Remove punctuation but keep spaces
    text = re.sub(r'\s+', ' ', text).strip()  # Remove extra spaces
    
    lemmatizer = WordNetLemmatizer()
    stop_words = set(stopwords.words('english'))
    words = text.split()
    cleaned = [lemmatizer.lemmatize(word) for word in words if word not in stop_words and len(word) > 2]
    return ' '.join(cleaned)

def plot_history(history):
    """Plot training history"""
    plt.figure(figsize=(12, 4))
    
    plt.subplot(1, 2, 1)
    plt.plot(history.history['accuracy'], label='Train Accuracy')
    plt.plot(history.history['val_accuracy'], label='Validation Accuracy')
    plt.title('Model Accuracy')
    plt.ylabel('Accuracy')
    plt.xlabel('Epoch')
    plt.legend()
    
    plt.subplot(1, 2, 2)
    plt.plot(history.history['loss'], label='Train Loss')
    plt.plot(history.history['val_loss'], label='Validation Loss')
    plt.title('Model Loss')
    plt.ylabel('Loss')
    plt.xlabel('Epoch')
    plt.legend()
    
    plt.tight_layout()
    plt.savefig('training/lstm_training_history.png')
    plt.close()

def main():
    # Load and preprocess data
    df = pd.read_csv(DATA_PATH)
    df.dropna(subset=['review', 'sentiment'], inplace=True)
    df['review'] = df['review'].astype(str)
    df['sentiment'] = df['sentiment'].str.lower()
    
    # Check class distribution
    print("\nClass Distribution:")
    print(df['sentiment'].value_counts())
    
    # Clean text
    df['cleaned_review'] = df['review'].apply(clean_text)
    
    # Label encoding
    label_map = {'positive': 1, 'negative': 0}
    df = df[df['sentiment'].isin(label_map.keys())]
    df['label'] = df['sentiment'].map(label_map)
    
    # Calculate class weights to handle imbalance
    import numpy as np

    class_weights = compute_class_weight(
        class_weight='balanced',
        classes=np.array([0, 1]),  # ✅ convert list to np.array
        y=df['label'].values       # ✅ use .values to ensure it's a NumPy array
    )

    class_weights = {i: class_weights[i] for i in range(len(class_weights))}
    print(f"\nClass Weights: {class_weights}")
    
    # Tokenization
    tokenizer = Tokenizer(num_words=MAX_VOCAB, oov_token="&lt;OOV&gt;")
    tokenizer.fit_on_texts(df['cleaned_review'])
    word_index = tokenizer.word_index
    print(f"\nFound {len(word_index)} unique tokens")
    
    sequences = tokenizer.texts_to_sequences(df['cleaned_review'])
    padded = pad_sequences(sequences, maxlen=MAX_LEN, padding='post', truncating='post')
    
    X = padded
    y = df['label'].values
    
    # Train/Test split
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.3, stratify=y, random_state=42
    )
    
    # Validation split from training
    X_train, X_val, y_train, y_val = train_test_split(
        X_train, y_train, test_size=0.1, stratify=y_train, random_state=42
    )
    
    # Build improved LSTM model
    model = Sequential([
        Embedding(MAX_VOCAB, EMBEDDING_DIM, input_length=MAX_LEN),
        Dropout(0.2),
        Bidirectional(LSTM(64, return_sequences=True, kernel_regularizer=l2(0.01))),
        Dropout(0.3),
        Bidirectional(LSTM(32)),
        Dropout(0.3),
        Dense(32, activation='relu', kernel_regularizer=l2(0.01)),
        Dropout(0.2),
        Dense(1, activation='sigmoid')
    ])
    
    model.compile(
        loss='binary_crossentropy',
        optimizer='adam',
        metrics=['accuracy']
    )
    
    # Callbacks
    callbacks = [
        EarlyStopping(monitor='val_loss', patience=3, restore_best_weights=True),
        ModelCheckpoint(MODEL_PATH, monitor='val_loss', save_best_only=True)
    ]
    
    # Train model
    print("\nTraining model...")
    history = model.fit(
        X_train, y_train,
        epochs=EPOCHS,
        batch_size=BATCH_SIZE,
        validation_data=(X_val, y_val),
        class_weight=class_weights,
        callbacks=callbacks,
        verbose=1
    )
    
    # Plot training history
    plot_history(history)
    
    # Evaluate
    loss, accuracy = model.evaluate(X_test, y_test)
    print(f"\nTest Accuracy: {accuracy:.4f}")
    
    # Save model and tokenizer
    model.save(MODEL_PATH)
    joblib.dump(tokenizer, TOKENIZER_PATH)
    
    print(f"\n✅ Improved LSTM model saved to {MODEL_PATH}")
    print(f"✅ Tokenizer saved to {TOKENIZER_PATH}")

if __name__ == '__main__':
    main()

from sklearn.metrics import classification_report
import json

# Predict for report
y_pred = (model.predict(X_test) > 0.5).astype("int32")

# Classification report
report = classification_report(y_test, y_pred, output_dict=True, target_names=['negative', 'positive'])

# Save report
with open("models/lstm_metrics.json", "w") as f:
    json.dump(report, f, indent=4)

print("\n📊 Metrics saved to models/lstm_metrics.json")
