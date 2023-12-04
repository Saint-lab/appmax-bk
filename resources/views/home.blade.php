<!DOCTYPE html>
<html>
   <head>
      <title>How To Integrate Stripe Payment Gateway In Laravel 8 - Techsolutionstuff</title>
      <meta name="_token" content="{{ csrf_token() }}" />
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>      
   </head>
   <body>
      <div class="container">         
         <div class="row">
            <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <center>
                <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()" class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>
            </center>
            <div class="card">
                <div class="card-header">{{ 'Dashboard' }}</div>
  
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
  
                    <form action="{{ route('send.notification') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="form-group">
                            <label>Body</label>
                            <textarea class="form-control" name="body"></textarea>
                          </div>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
  
                </div>
            </div>
        </div>
    </div>
</div>
  
<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>
<!-- <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script> -->


<script>
  
    var firebaseConfig = {
        apiKey: "AIzaSyBvN6Pgvd-6RAE1cAMIOR7Ypu256qsVF5U",
        authDomain: "appmax-4d8b0.firebaseapp.com",
        databaseURL: "https://XXXX.firebaseio.com",
        projectId: "appmax-4d8b0",
        storageBucket: "appmax-4d8b0.appspot.com",
        messagingSenderId: "147477820101",
        appId: "1:147477820101:web:bf09f41f7f2dc9b19b76ac",
        measurementId: "G-7B9XG4BRK4"
    };
      
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();
  console.log(messaging);
    function initFirebaseMessagingRegistration() {
            messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function(pin) {
                console.log(pin);
   
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
  
                $.ajax({
                    url: '{{ route("fcmToken") }}',
                    type: 'POST',
                    data: {
                      pin: pin,
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        alert('Token saved successfully.');
                    },
                    error: function (err) {
                        console.log('User Chat Token Error'+ err);
                    },
                });
  
            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
     }  
      
    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });
   
</script>
         </div>
      </div>
   </body>   
</html>
