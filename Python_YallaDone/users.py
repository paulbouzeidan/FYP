import mysql.connector
import pandas as pd
from datetime import datetime

def connect_to_database():
    try:
        connection = mysql.connector.connect(
            host='127.0.0.1',
            user='root',
            password='',  # Add your password if necessary
            database='yalladone'
        )
        print("Database connection successful.")
        return connection
    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None

def read_table(connection, table_name):
    try:
        query = f"SELECT * FROM {table_name}"
        df = pd.read_sql(query, connection)
        return df
    except Exception as err:
        print(f"Error: {err}")
        return None

def calculate_age(birthday):
    today = datetime.today()
    age = today.year - birthday.year - ((today.month, today.day) < (birthday.month, birthday.day))
    return age

def analyze_users(df):
    # Number of users
    num_users = df.shape[0]
    
    # Calculate age of users and average age
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
        print(f"Error: {err}")
        return None

def analyze_payments(df):
    df['created_at'] = pd.to_datetime(df['created_at'])
    df['month'] = df['created_at'].dt.to_period('M')
    df['day'] = df['created_at'].dt.to_period('D')

    monthly_income = df.groupby('month')['price'].sum()
    daily_income = df.groupby('day')['price'].sum()

    return monthly_income, daily_income

def analyze_addresses(df):
    most_frequent_city = df['city'].value_counts().idxmax()
    return most_frequent_city

def analyze_payment_methods(df):
    payment_method_counts = df['type'].value_counts()
    most_popular_method = payment_method_counts.idxmax() if not payment_method_counts.empty else 'None'

    return payment_method_counts, most_popular_method

def export_to_text(data, filename):
    with open(filename, 'w') as file:
        for key, value in data.items():
            file.write(f"{key}: {value}\n")
        file.write("\n")

def export_to_excel(data, filename):
    with pd.ExcelWriter(filename, engine='xlsxwriter') as writer:
        for key, value in data.items():
            if isinstance(value, pd.Series):
                value.to_frame().to_excel(writer, sheet_name=key)
            else:
                pd.DataFrame({key: [value]}).to_excel(writer, sheet_name=key)

def main():
    connection = connect_to_database()
    if connection is None:
        print("Failed to connect to the database.")
        return

    results = {}

    users_df = read_table(connection, 'users')
    if users_df is not None:
        user_analysis = analyze_users(users_df)
        results.update(user_analysis)
    else:
        print("Failed to read users table.")

    best_selling_service = analyze_services(connection)
    if best_selling_service:
        results['Best-selling service'] = best_selling_service

    payments_df = read_table(connection, 'payments')
    if payments_df is not None:
        monthly_income, daily_income = analyze_payments(payments_df)
        results['Monthly Income'] = monthly_income
        results['Daily Income'] = daily_income
        
        payment_methods_count, most_popular_method = analyze_payment_methods(payments_df)
        results['Payment Methods Count'] = payment_methods_count
        results['Most Popular Payment Method'] = most_popular_method
    else:
        print("Failed to read payments table.")

    addresses_df = read_table(connection, 'addresses')
    if addresses_df is not None:
        most_frequent_city = analyze_addresses(addresses_df)
        results['Most frequent city using the app'] = most_frequent_city
    else:
        print("Failed to read addresses table.")

    export_to_text(results, 'analysis_results.txt')
    export_to_excel(results, 'analysis_results.xlsx')

    connection.close()
    print("Database connection closed.")

if __name__ == "__main__":
    main()
