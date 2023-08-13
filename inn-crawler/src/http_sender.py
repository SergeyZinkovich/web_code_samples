import requests

URL = "https://service.nalog.ru/inn-new-proc.json"

NEED_REPEAT_ANS = 'Недопустимый идентификатор запроса'
NEED_REPEAT_STATUS = 'Need repeat'
INN_NOT_FOUND_STATUS = 'Inn not found'
WRONG_REQUEST_STATUS = 'wrong request status'


def get_result(request_id: str):
    params = {'c': 'get', 'requestId': request_id}

    try:
        ans = requests.get(url=URL, params=params).json()
    except Exception as e:
        return None

    if 'ERROR' in ans and ans['ERROR'] == NEED_REPEAT_ANS:
        return NEED_REPEAT_STATUS

    if 'error_code' in ans:
        return INN_NOT_FOUND_STATUS

    return ans['inn']


def create_request(fam: str, nam: str, otch: str, bdate: str, docno: str, docdt: str):
    params = {
        'c': 'find',
        'fam': fam,
        'nam': nam,
        'bdate': bdate,
        'docno': docno,
        'docdt': docdt
    }

    if docno[2] != '-':
        params['doctype'] = '21'
    else:
        params['doctype'] = '01'

    if otch:
        params['otch'] = otch
    else:
        params['opt_otch'] = 1

    try:
        ans = requests.post(url=URL, data=params).json()
    except Exception as e:
        return None

    if 'ERROR' in ans or 'ERRORS' in ans:
        return WRONG_REQUEST_STATUS

    return ans['requestId']
