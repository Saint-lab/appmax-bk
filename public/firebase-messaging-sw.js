importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-messaging.js');


   
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
        apiKey: "AIzaSyBvN6Pgvd-6RAE1cAMIOR7Ypu256qsVF5U",
        authDomain: "appmax-4d8b0.firebaseapp.com",
        //databaseURL: "https://XXXX.firebaseio.com",
        projectId: "appmax-4d8b0",
        storageBucket: "appmax-4d8b0.appspot.com",
        messagingSenderId: "147477820101",
        appId: "1:147477820101:web:bf09f41f7f2dc9b19b76ac",
        measurementId: "G-7B9XG4BRK4"
    });
  
/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "/itwonders-web-logo.png",
    };
  
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});