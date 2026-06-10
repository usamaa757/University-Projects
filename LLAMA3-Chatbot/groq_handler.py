import requests
import time
import os
from dotenv import load_dotenv

load_dotenv()

class GroqHandler:
    def __init__(self):
        self.api_key = os.getenv('GROQ_API_KEY')
        self.api_url = "https://api.groq.com/openai/v1/chat/completions"
        
        # ✅ ACTIVE MODELS (as of 2026)
        # Choose one:
        self.model = "llama-3.3-70b-versatile"  # Best quality, latest
        # self.model = "llama3-70b-8192"        # Original LLaMA 3
        # self.model = "mixtral-8x7b-32768"     # Very fast
        # self.model = "gemma2-9b-it"           # Google's model
        
        self.conversation_history = {}
        
        print(f"🔧 Initialized Groq Handler")
        print(f"🤖 Model: {self.model}")
        print(f"🔑 API Key: {'✅ Present' if self.api_key else '❌ Missing'}")
    
    def is_available(self):
        """Check if API key is available"""
        if not self.api_key:
            print("❌ No API key found")
            return False
        
        print("📡 Testing API connection...")
        try:
            headers = {
                "Authorization": f"Bearer {self.api_key}",
                "Content-Type": "application/json"
            }
            
            payload = {
                "model": self.model,
                "messages": [{"role": "user", "content": "Say OK"}],
                "max_tokens": 5
            }
            
            response = requests.post(
                self.api_url,
                headers=headers,
                json=payload,
                timeout=10
            )
            
            if response.status_code == 200:
                print("✅ Groq API is available")
                return True
            else:
                print(f"❌ API test failed: {response.status_code}")
                print(f"Response: {response.text}")
                return False
                
        except Exception as e:
            print(f"❌ API connection error: {e}")
            return False
    
    def generate_response(self, prompt, session_id, temperature=0.7, max_tokens=500):
        """Generate response using Groq API"""
        
        print(f"\n📝 Generating response for: {prompt[:50]}...")
        
        # Initialize conversation history for new session
        if session_id not in self.conversation_history:
            self.conversation_history[session_id] = []
        
        # Add user message to history
        self.conversation_history[session_id].append({
            "role": "user", 
            "content": prompt
        })
        
        # Prepare messages (last 20 for context)
        messages = self.conversation_history[session_id][-20:]
        
        # Call Groq API
        start_time = time.time()
        
        try:
            headers = {
                "Authorization": f"Bearer {self.api_key}",
                "Content-Type": "application/json"
            }
            
            payload = {
                "model": self.model,
                "messages": messages,
                "temperature": temperature,
                "max_tokens": max_tokens,
                "top_p": 1,
                "stream": False
            }
            
            print(f"🚀 Sending to Groq API...")
            response = requests.post(
                self.api_url,
                headers=headers,
                json=payload,
                timeout=30
            )
            
            response_time = time.time() - start_time
            print(f"📡 Response status: {response.status_code} (Time: {response_time:.2f}s)")
            
            if response.status_code == 200:
                result = response.json()
                bot_response = result['choices'][0]['message']['content']
                
                print(f"✅ Response: {bot_response[:100]}...")
                
                # Add bot response to history
                self.conversation_history[session_id].append({
                    "role": "assistant",
                    "content": bot_response
                })
                
                return bot_response, response_time
            else:
                error_msg = f"API Error: {response.status_code}"
                print(f"❌ {error_msg}")
                print(f"Details: {response.text}")
                return f"Sorry, I encountered an error. {error_msg}", response_time
                
        except requests.exceptions.Timeout:
            print("❌ Request timed out")
            return "Request timed out. Please try again.", 0
        except Exception as e:
            print(f"❌ Exception: {e}")
            return f"Error: {str(e)}", 0
    
    def clear_history(self, session_id):
        """Clear conversation history for a session"""
        if session_id in self.conversation_history:
            del self.conversation_history[session_id]
            print(f"🗑️ Cleared history for session {session_id}")