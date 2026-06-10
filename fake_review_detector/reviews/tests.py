from nltk.sentiment.vader import SentimentIntensityAnalyzer
import nltk

# Download once
nltk.download('vader_lexicon')

def analyze_sentiment_vader(text):
    sia = SentimentIntensityAnalyzer()
    score = sia.polarity_scores(text)

    compound = score['compound']

    if compound >= 0.05:
        return 'positive', compound
    elif compound <= -0.05:
        return 'negative', compound
    return 'neutral', compound


# Call the function
sentiment, compound = analyze_sentiment_vader("very good")

print("Sentiment:", sentiment)
print("Compound score:", compound)
