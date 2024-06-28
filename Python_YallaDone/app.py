from flask import Flask, jsonify
import mysql.connector
import pandas as pd
from datetime import datetime
import argparse

app = Flask(__name__)

def connect_to_database():
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            user='root',
            password='',  # Add your password if necessary
            database='yalladone'
        )
        return connection
    except mysql.connector.Error as err:
        return None

def read_table(connection, table_name):
    try:
        query = f"SELECT * FROM {table_name}"
        df = pd.read_sql(query, connection)
        return df
    except Exception as err:
        return None

def calculate_age(birthday):
    today = datetime.today()
    age = today.year - birthday.year - ((today.month, today.day) < (birthday.month, birthday.day))
    return age

def analyze_users(df):
    num_users = df.shape[0]
    
    if 'birthday' in df.columns:
        df['birthday'] = pd.to_datetime(df['birthday'])
        df['age'] = df['birthday'].apply(calculate_age)
        average_age = df['age'].mean()
    else:
        average_age = None
    
    return {
        "Number of users": num_users,
        "Average age of users": average_age
    }

def analyze_services(connection):
    try:
        query = """
            SELECT s.service_name, COUNT(sf.Service_id) AS service_count
            FROM services_forms sf
            JOIN services s ON sf.Service_id = s.service_id
            GROUP BY s.service_name
            ORDER BY service_count DESC
            LIMIT 1
        """
        df = pd.read_sql(query, connection)
        best_selling_service = df.iloc[0]['service_name'] if not df.empty else 'None'
        return best_selling_service
    except Exception as err:
        return None

def analyze_payments(df):
    df['created_at'] = pd.to_datetime(df['created_at'])
    df['month'] = df['created_at'].dt.to_period('M').astype(str)
    df['day'] = df['created_at'].dt.to_period('D').astype(str)

    monthly_income = df.groupby('month')['price'].sum()
    daily_income = df.groupby('day')['price'].sum()

    return monthly_income, daily_income

def analyze_addresses(df):
    most_frequent_city = df['city'].value_counts().idxmax()
    return most_frequent_city

def analyze_payment_methods(df):
    payment_method_counts = df['type'].value_counts()
    most_popular_method = payment_method_counts.idxmax() if not payment_method_counts.empty else 'None'

    return payment_method_counts.to_dict(), most_popular_method

@app.route('/analyze', methods=['GET'])
def analyze():
    connection = connect_to_database()
    if connection is None:
        return jsonify({"error": "Failed to connect to the database"}), 500

    results = {}

    users_df = read_table(connection, 'users')
    if users_df is not None:
        user_analysis = analyze_users(users_df)
        results.update(user_analysis)
    else:
        return jsonify({"error": "Failed to read users table"}), 500

    best_selling_service = analyze_services(connection)
    if best_selling_service:
        results['Best-selling service'] = best_selling_service

    payments_df = read_table(connection, 'payments')
    if payments_df is not None:
        monthly_income, daily_income = analyze_payments(payments_df)
        results['Monthly Income'] = monthly_income.to_dict()
        results['Daily Income'] = daily_income.to_dict()
        
        payment_methods_count, most_popular_method = analyze_payment_methods(payments_df)
        results['Payment Methods Count'] = payment_methods_count
        results['Most Popular Payment Method'] = most_popular_method
    else:
        return jsonify({"error": "Failed to read payments table"}), 500

    addresses_df = read_table(connection, 'addresses')
    if addresses_df is not None:
        most_frequent_city = analyze_addresses(addresses_df)
        results['Most frequent city using the app'] = most_frequent_city
    else:
        return jsonify({"error": "Failed to read addresses table"}), 500

    connection.close()

    return jsonify(results)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Run Flask server.')
    parser.add_argument('--host', type=str, default='127.0.0.1', help='Host address of the server')
    parser.add_argument('--port', type=int, default=5000, help='Port number of the server')
    args = parser.parse_args()

    app.run(host=args.host, port=args.port, debug=True)