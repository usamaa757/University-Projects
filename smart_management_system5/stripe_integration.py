# stripe_integration.py
import stripe

class StripePayment:
    def __init__(self, api_key):
        stripe.api_key = api_key
    
    def create_customer(self, email, name):
        try:
            customer = stripe.Customer.create(
                email=email,
                name=name
            )
            return customer
        except Exception as e:
            print(f"Error creating customer: {e}")
            return None
    
    def create_payment_intent(self, amount, currency='pkr', customer_id=None):
        try:
            intent = stripe.PaymentIntent.create(
                amount=int(amount * 100),  # Convert to cents
                currency=currency,
                customer=customer_id,
                automatic_payment_methods={
                    'enabled': True,
                },
            )
            return intent
        except Exception as e:
            print(f"Error creating payment intent: {e}")
            return None
    
    def confirm_payment(self, payment_intent_id):
        try:
            intent = stripe.PaymentIntent.retrieve(payment_intent_id)
            return intent.status == 'succeeded'
        except Exception as e:
            print(f"Error confirming payment: {e}")
            return False