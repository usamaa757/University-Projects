import requests

def scrape_data(keyword, seller_type, seller_country):
    api_key = 'AIzaSyBEOQ8k_ErNxz3gNXBIRKXnDtrL6lMNS9I'
    cx = '8323b863c0c6f43cc'
    query = f"{keyword} {seller_type} {seller_country}"
    
    url = f"https://www.googleapis.com/customsearch/v1"
    params = {
        'key': api_key,
        'cx': cx,
        'q': query
    }
    
    response = requests.get(url, params=params)
    
    if response.status_code != 200:
        return None
    
    results = response.json()
    
    listings = []
    for item in results.get('items', []):
        title = item.get('title')
        snippet = item.get('snippet')
        link = item.get('link')
        
        listings.append({
            'title': title,
            'description': snippet,
            'url': link
        })
    
    return listings
