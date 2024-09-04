var app = require('express')();
const log = require('node-file-logger');
var server = require('http').Server(app);
var io = require('socket.io')(server,{
    cors: {
      origin: true,
      methods: ["GET", "POST"],
      credentials:true
    },
    transports: ['websocket', 'polling'],
    allowEIO3: true
  });

var redis = require('redis');


server.listen(3000);
io.on('connection',function (socket) {
    console.log("client connected ",socket.id);   
    var redisClient = redis.createClient({
        url: 'redis://redis:6379' 
      });
    redisClient.subscribe('create:blog');

    redisClient.on("message", function (channel,data) {
        console.log("new Message add in queue "+data['message']+" channel ");
        socket.emit(channel,data);
    });

    socket.on("disconnect",function () {
        // redisClient.quit();
    });
});

(async() => {
    const client = redis.createClient({
        url: 'redis://redis:6379' 
      });
    const subscribe = client.duplicate();

    await subscribe.connect();

    await subscribe.subscribe('create:blog',(message)=>{
        // log.Info(message);
        const event = JSON.parse(message);
        log.Info(event.data);
        io.emit('new-message',
          JSON.stringify(event.data)
        );
    });
})();