from datetime import datetime
import psycopg2
from psycopg2.extras import DictCursor
from dotenv import load_dotenv
import os

DB_SCHEMA_PATH = 'resources/schema.sql'

NEED_TO_GET_REQUEST_ID_STATUS = 0
WAITING_RESULT_STATUS = 1
INN_PARSED_STATUS = 2
NEED_REPEAT_STATUS = 3
WRONG_REQUEST_STATUS = 4
INN_NOT_FOUND_STATUS = 5

dotenv_path = os.path.join(os.path.dirname(__file__), '.env')
if os.path.exists(dotenv_path):
    load_dotenv(dotenv_path)


def init_db():
    conn = get_db_connection()
    cur = conn.cursor()
    conn.autocommit = False

    with open(DB_SCHEMA_PATH) as f:
        cur.execute(f.read())

    conn.commit()
    cur.close()
    conn.close()


def get_db_connection():
    conn = psycopg2.connect(
        host=os.getenv('DB_HOST'),
        database=os.getenv('DB_NAME'),
        user=os.getenv('DB_USERNAME'),
        password=os.getenv('DB_PASSWORD'))

    return conn


def serialize_request(row):
    row = dict(row)
    row['birthday'] = row['birthday'].strftime('%d.%m.%Y')

    if 'passport_date' in row:
        row['passport_date'] = row['passport_date'].strftime('%d.%m.%Y')

    return row


def deserialize_request(row):
    row[3] = datetime.strptime(row[3], '%d.%m.%Y')
    if row[4][0].isdigit():
        row[4] = row[4].replace(' ', '')
        row[4] = row[4][:2] + ' ' + row[4][2:4] + ' ' + row[4][4:]
    else:
        row[4] = row[4].replace(' ', '')
        row[4] = row[4].replace('-', '')
        row[4] = row[4][:2] + '-' + row[4][2:4] + ' ' + row[4][4:]

    if row[5]:
        row[5] = datetime.strptime(row[5], '%d.%m.%Y')

    return tuple(row)


def get_requests():
    conn = get_db_connection()
    cur = conn.cursor()
    requests = cur.execute('SELECT * FROM requests').fetchall()
    conn.commit()
    cur.close()
    conn.close()

    return [serialize_request(ix) for ix in requests]


def get_requests_by_status(status: int):
    conn = get_db_connection()
    cur = conn.cursor(cursor_factory=DictCursor)
    cur.execute('SELECT * FROM requests WHERE status = %s', (status,))
    requests = cur.fetchall()
    conn.commit()
    cur.close()
    conn.close()

    return [serialize_request(ix) for ix in requests]


def get_request_by_model_id(id: int):
    conn = get_db_connection()
    cur = conn.cursor(cursor_factory=DictCursor)
    cur.execute('SELECT * FROM requests WHERE id = %s', (id,))
    request = cur.fetchone()
    conn.commit()
    cur.close()
    conn.close()

    return serialize_request(request)


def get_request_by_model_ids(ids: list):
    conn = get_db_connection()
    cur = conn.cursor(cursor_factory=DictCursor)
    cur.execute(f'SELECT * FROM requests WHERE id in %s', (tuple(ids),))
    requests = cur.fetchall()
    conn.commit()
    cur.close()
    conn.close()

    return [serialize_request(ix) for ix in requests]


def model_exists(ids: list):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute(f'SELECT id FROM requests WHERE id in %s', (tuple(ids),))
    requests = cur.fetchall()
    conn.commit()
    cur.close()
    conn.close()

    return requests and len(requests) == len(ids)


def create_request(params: list):
    sql = ''' INSERT INTO requests(first_name, second_name, last_name, birthday, passport_number, passport_date)
                    VALUES(%s,%s,%s,%s,%s,%s) RETURNING id '''

    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute(sql, deserialize_request(params))
    id = cur.fetchone()[0]
    conn.commit()
    cur.close()
    conn.close()

    return id


def add_request_id(model_id: int, request_id: str):
    _update_request(model_id, request_id, 'request_id')


def add_inn(model_id: int, inn: str):
    _update_request(model_id, inn, 'inn')


def set_status(model_id: int, status: int):
    _update_request(model_id, status, 'status')


def _update_request(model_id: int, value, field: str):
    sql = f''' UPDATE requests SET {field} = %s WHERE id=%s '''
    params = (
        value,
        model_id
    )

    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute(sql, params)
    conn.commit()
    cur.close()
    conn.close()
