<?php
namespace App\Services\Paystack\Payment;
trait PaystackPaymentMockedWebhook{

    function chargeSuccessful()
    {
        return '{  
            "event":"charge.success",
            "data": {  
              "id":302961,
              "domain":"live",
              "status":"success",
              "reference":"qTPrJoy9Bx",
              "amount":10000,
              "message":null,
              "gateway_response":"Approved by Financial Institution",
              "paid_at":"2016-09-30T21:10:19.000Z",
              "created_at":"2016-09-30T21:09:56.000Z",
              "channel":"card",
              "currency":"NGN",
              "ip_address":"41.242.49.37",
              "metadata":0,
              "log":{  
                "time_spent":16,
                "attempts":1,
                "authentication":"pin",
                "errors":0,
                "success":false,
                "mobile":false,
                "input":[],
                "channel":null,
                "history":[  
                  {  
                    "type":"input",
                    "message":"Filled these fields: card number, card expiry, card cvv",
                    "time":15
                  },
                  {  
                    "type":"action",
                    "message":"Attempted to pay",
                    "time":15
                  },
                  {  
                    "type":"auth",
                    "message":"Authentication Required: pin",
                    "time":16
                  }
                ]
              },
              "fees":null,
              "customer":{  
                "id":68324,
                "first_name":"BoJack",
                "last_name":"Horseman",
                "email":"bojack@horseman.com",
                "customer_code":"CUS_qo38as2hpsgk2r0",
                "phone":null,
                "metadata":null,
                "risk_action":"default"
              },
              "authorization":{  
                "authorization_code":"AUTH_f5rnfq9p",
                "bin":"539999",
                "last4":"8877",
                "exp_month":"08",
                "exp_year":"2020",
                "card_type":"mastercard DEBIT",
                "bank":"Guaranty Trust Bank",
                "country_code":"NG",
                "brand":"mastercard",
                "account_name": "BoJack Horseman"
              },
              "plan":{}
            } 
          }';
    }
}