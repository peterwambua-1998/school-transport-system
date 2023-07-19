<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>pusher beams</title>
</head>
<body>
  <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>
  <script defer>
    let user_id = "{{Auth::user()->id}}";
    console.log(user_id);
    
    const beamsClient = new PusherPushNotifications.Client({
      instanceId: "c880bb01-d93f-4eb8-9fd1-0a3003477735",
    });
    beamsClient
    .start()
    .then((beamsClient) => beamsClient.getDeviceId())
    .then((deviceId) => console.log("Successfully registered with Beams. Device ID:", deviceId))
    .then(() => beamsClient.addDeviceInterest(`transport-${user_id}`))
    .then(() => beamsClient.getDeviceInterests())
    .then((interests) => console.log("Current interests:", interests))
    .catch(console.error);
    
  </script>
</body>
</html>