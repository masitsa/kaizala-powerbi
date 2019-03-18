# Kaizala Reporting using 3rd Party App

Kaizala is a mobile application that allows work get done within the context of a conversation / group using actions. While it provides a whole lot of functionalities that allow businesses and enterprises to get work done, it also supports extensiblity allowing building of powerful integrations with existing business processes / workflows.

### Getting Started...

To work with Kaizala APIs, a connector is required. Kaizala Connectors enable the integration of Kaizala into 3rd party business processes by providing the ability to perform a set of actions using REST based API calls. For more on getting started with Kaizala APIs, read [this - Kaizala](https://kaizala007.blog/2017/12/30/getting-started-with-kaizala-apis/)
or [this-microsoft](https://docs.microsoft.com/en-us/kaizala/connectors/readme)
Now that you have registered a connector to get the applicationId, applicationSecret and refreshToken, we can use them to generate accessToken for authenticating to Kaizala.

### Integration

##### Webhooks

Webhooks allow you to build or integrate applications which subscribe to certain events on Kaizala. When one of those events is triggered, Kaizala service would send a HTTPS POST payload to the webhook’s configured URL. Webhooks can be used to listen to content being posted on the group and use that information to update your database, trigger workflows in your internal systems, etc.

###### To register a webhook and update database with content posted on group

The following are required:

- accessToken - Generated using applicationId, applicationSecret and refreshToken
- objectId - in this case, it is groupId
- objectType - it will be Group for the sake of getting content posted on group
- eventTypes - different types of events to subscribe for
- callbackUrl - HTTPS URL to which the subscribed events need to be notified to

For example,
Request Body - Subscribe to all events at group level

```sh
{
   "objectId":"74943849802190eaea3810",
   "objectType":"Group",
   "eventTypes":[
      "ActionCreated",
      "ActionResponse",
      "SurveyCreated",
      "JobCreated",
      "SurveyResponse",
      "JobResponse",
      "TextMessageCreated",
      "AttachmentCreated",
      "Announcement",
      "MemberAdded",
      "MemberRemoved",
      "GroupAdded",
      "GroupRemoved"
   ],
   "callBackUrl":"https://requestb.in/123",
   "callBackToken":"tokenToBeVerifiedByCallback",
   "callBackContext":"Any data which is required to be returned in callback"
}
```

Note that: To ensure that your webhook service endpoint is authentic and working, your callback URL will be verified before creating subscription. For verification, Kaizala will generate a validation token and send a GET request to your webhook with a query parameter “validationToken” which you need to send back within 5 seconds. [Read More](https://docs.microsoft.com/en-us/kaizala/connectors/webhookvalidaton)

Once the webhook is registered successfully and one of the subscribed events is triggered, the callback payload (Request Body) (a JSON) will be sent to your callbackUrl and you will have to parse the JSON content to get the data of interest.

Sample JSON sent to callbackURL when a survey is created:

```sh
{
  "subscriptionId": "e312f80d-e6f1-4d06-beb2-90eaea3810",
  "objectId": "29b9bf47-9249-90eaea3810-97fa940a29f7",
  "objectType": "Group",
  "eventType": "SurveyCreated",
  "eventId": "90eaea3810-4bb4-946d-298ad53f27ba",
  "data": {
    "actionId": "bd06606e-90eaea3810-93-946d-298ad53f27ba",
    "groupId": "29b9bf47-9249-90eaea3810-97fa940a29f7",
    "validity": 1553505316037,
    "title": "Market Research Day",
    "visibility": "All",
    "questions": [
      {
        "title": "Will you be available? ",
        "type": "SingleOption",
        "options": [
          {
            "title": "Yes"
          },
          {
            "title": "No"
          },
          {
            "title": "Come late"
          }
        ]
      },
      {
        "isInvisible": true,
        "title": "ResponseTime",
        "type": "DateTime",
        "options": []
      },
      {
        "isInvisible": true,
        "title": "ResponseLocation",
        "type": "Location",
        "options": []
      }
    ],
    "properties": [
      {
        "name": "DateTime",
        "type": "Numeric",
        "value": "1"
      },
      {
        "name": "Location",
        "type": "Numeric",
        "value": "2"
      },
      {
        "name": "Description",
        "type": "Text",
        "value": "List of all members available "
      }
    ]
  },
  "context": "YOUR_CALLBACK_URL",
  "fromUser": "+2547xxxxxxxx",
  "fromUserId": "drt36972-32f6-84e1-91b6-53sdfe9403f6",
  "isBotfromUser": false,
  "fromUserName": "Samuel Wanjohi",
  "fromUserProfilePic": "",
  "groupId": "29b9bf47-9249-90eaea3810-97fa940a29f7",
  "sourceGroupId": "29b9bf47-9249-90eaea3810-97fa940a29f7"
}
```
