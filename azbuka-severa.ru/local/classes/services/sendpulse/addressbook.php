<?php

namespace Dev\SendPulse;


class AddressBookController extends BasicController
{
    protected static $bookId = 627506;

    public static function getList()
    {

    }

    public static function getEmailList()
    {
        $url = 'addressbooks/'.self::$bookId.'/emails';
        $tokenData = BasicController::getToken();
        $tokenData = json_decode($tokenData, 1);
        if ($tokenData['status'] == 'ok' && $tokenData['token']) {
            $pulseRequest = BasicController::sendRequest('GET', $url, [], $tokenData['token']);
            return json_decode($pulseRequest, 1);
        } else {
            return 'bad token';
        }
    }

    private static function checkEmail($email = '', $arEmails = [])
    {
        return in_array($email, $arEmails);
    }

    public static function addEmail($data = [])
    {
        $method = 'addressbooks/'.self::$bookId.'/emails';
        $tokenData = BasicController::getToken();
        $tokenData = json_decode($tokenData, 1);
        if ($tokenData['status'] == 'ok' && $tokenData['token']) {
            $arEmails = self::getEmailList();
            foreach ($data['emails'] as $key => $email) {
                if (self::checkEmail($email, $arEmails)) {
                    unset($data['emails'][$key]);
                }
            }
            $pulseRequest = BasicController::sendRequest('POST', $method, $data, $tokenData['token']);
            return json_decode($pulseRequest, 1);
        } else {
            return 'bad token';
        }
    }

    public static function addEmailMigrate($data = [])
    {
        $method = 'addressbooks/'.self::$bookId.'/emails';
        $tokenData = BasicController::getToken();
        $tokenData = json_decode($tokenData, 1);
        if ($tokenData['status'] == 'ok' && $tokenData['token']) {
            $arEmails = self::getEmailList();
            foreach ($data['emails'] as $key => $email) {
                if (self::checkEmail($email['email'], $arEmails)) {
                    unset($data['emails'][$key]);
                }
            }
            $pulseRequest = BasicController::sendRequest('POST', $method, $data, $tokenData['token']);
            return json_decode($pulseRequest, 1);
        } else {
            return 'bad token';
        }
    }

    public static function migrate()
    {
        $url = 'addressbooks/447848/emails?limit=100&offset=700';
        $tokenData = BasicController::getToken();
        $tokenData = json_decode($tokenData, 1);
        if ($tokenData['status'] == 'ok' && $tokenData['token']) {
            $pulseRequest = BasicController::sendRequest('GET', $url, [], $tokenData['token']);
            return json_decode($pulseRequest, 1);
        } else {
            return 'bad token';
        }
    }
}