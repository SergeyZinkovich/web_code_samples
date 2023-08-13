from . import http_sender
from . import db


def update_request_with_request_id(model_id: int):
    request = db.get_request_by_model_id(model_id)

    request_id = http_sender.create_request(
        request['last_name'],
        request['first_name'],
        request['second_name'],
        request['birthday'],
        request['passport_number'],
        request['passport_date'],
    )

    if request_id == http_sender.WRONG_REQUEST_STATUS:
        db.set_status(model_id, db.WRONG_REQUEST_STATUS)
    elif request_id:
        db.add_request_id(model_id, request_id)
        db.set_status(model_id, db.WAITING_RESULT_STATUS)

        return request_id
    else:
        return None


def update_request_with_inn(model_id: int):
    request = db.get_request_by_model_id(model_id)

    ans = http_sender.get_result(
        request['request_id']
    )

    if ans == http_sender.NEED_REPEAT_STATUS:
        db.set_status(model_id, db.NEED_REPEAT_STATUS)
    elif ans == http_sender.INN_NOT_FOUND_STATUS:
        db.set_status(model_id, db.INN_NOT_FOUND_STATUS)
    elif ans:
        db.add_inn(model_id, ans)
        db.set_status(model_id, db.INN_PARSED_STATUS)

        return ans
    else:
        return None


def update_all_with_inn():
    for model in db.get_requests_by_status(db.NEED_REPEAT_STATUS):
        update_request_with_request_id(model['id'])
        update_request_with_inn(model['id'])

    for model in db.get_requests_by_status(db.WAITING_RESULT_STATUS):
        update_request_with_inn(model['id'])


def update_all_with_request_id():
    for model in db.get_requests_by_status(db.NEED_TO_GET_REQUEST_ID_STATUS):
        update_request_with_request_id(model['id'])
