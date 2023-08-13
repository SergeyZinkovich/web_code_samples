from flask import Flask
from flask import request
from cerberus import Validator
from datetime import datetime
from flask_crontab import Crontab

from src import db, request_service

app = Flask(__name__)
crontab = Crontab(app)


@app.route('/get_request_result', methods=['GET'])
def get_request_result():
    args = parse_multi_form(request.args)
    ids = args.get('request_id')

    if type(ids) == dict:
        try:
            ids = list(map(int, (ids.values())))
        except:
            ids = None
    elif type(ids) == str:
        try:
            ids = [int(ids)]
        except:
            ids = None

    if not ids:
        return {'error': 'Wrong request'}, 400

    if not validate_ids_exists(ids):
        return {'error': 'Request does\'t exist'}, 400

    return db.get_request_by_model_ids(ids)


@app.route('/create_request', methods=['POST'])
def create_request():
    schema = {
        'fam': {'type': 'string', 'required': True},
        'nam': {'type': 'string', 'required': True},
        'otch': {'type': 'string', 'required': False},
        'bdate': {'required': True},
        'docno': {'type': 'string', 'required': True},
        'docdt': {'required': False},
    }
    validator = Validator(schema)

    if not validator.validate(request.form) or not validate_creation_data(request.form):
        return {'error': 'Wrong request'}, 400

    model_id = db.create_request([
        request.form['nam'],
        request.form.get('otch'),
        request.form['fam'],
        request.form['bdate'],
        request.form['docno'],
        request.form.get('docdt'),
    ])

    request_id = request_service.update_request_with_request_id(model_id)

    response = {'request_id': model_id}

    if not request_id:
        response['status'] = 'destination site error'

    return response


def validate_creation_data(form):
    try:
        datetime.strptime(form['bdate'], '%d.%m.%Y')
        datetime.strptime(form['docdt'], '%d.%m.%Y')
    except Exception as e:
        return False

    return True


def validate_ids_exists(ids: list):
    return db.model_exists(ids)


def parse_multi_form(form):
    data = {}
    for url_k in form:
        v = form[url_k]
        ks = []
        while url_k:
            if '[' in url_k:
                k, r = url_k.split('[', 1)
                ks.append(k)
                if r[0] == ']':
                    ks.append('')
                url_k = r.replace(']', '', 1)
            else:
                ks.append(url_k)
                break
        sub_data = data
        for i, k in enumerate(ks):
            if k.isdigit():
                k = int(k)
            if i+1 < len(ks):
                if not isinstance(sub_data, dict):
                    break
                if k in sub_data:
                    sub_data = sub_data[k]
                else:
                    sub_data[k] = {}
                    sub_data = sub_data[k]
            else:
                if isinstance(sub_data, dict):
                    sub_data[k] = v

    return data


@app.cli.command('pars-id')
@crontab.job()
def parse_request_id():
    request_service.update_all_with_request_id()


@app.cli.command('pars-inn')
@crontab.job()
def parse_inn():
    request_service.update_all_with_inn()


@app.cli.command('init-db')
def init_db():
    db.init_db()


if __name__ == "__main__":
    app.run(host='0.0.0.0', debug=True)
