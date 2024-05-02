<!DOCTYPE html>
<html>

<head>
    <title>CliQ connection - Report mail</title>
</head>

<body>
    <p>Hello,</p>
    <p>
        User <b> {{ $data['from_user_name'] }} </b> has reported user <b> {{ $data['to_user_name'] }} </b> as using objectionable or abuse content which has been distributed through the application. Please investigate this incident and take the appropriate action to resolve.

        <br><br>

        For more details, login into web admin panel and select the “Reported User” Menu.
    </p>

</body>

</html>