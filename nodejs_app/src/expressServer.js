var app = require('express')();
// var cors = require('cors');

// app.use(cors());
// app.options('/*',cors());
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
        host: 'host.docker.internal',
        port: 6379
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
        host: 'host.docker.internal',
        port: 6379
      });
    const subscribe = client.duplicate();

    await subscribe.connect();

    await subscribe.subscribe('create:blog',(message)=>{
        console.log(message);
        // const event = JSON.parse(message);
        io.emit('new-message',
            message
        );
    });
})();