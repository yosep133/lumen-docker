var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8890);
io.on('connection',function (socket) {
    
    console.log('client connected');
    var redisClient = redis.createClient();
    redisClient.subscribe('create:blog');

    redisClient.on('create:blog',function (channel, data) {
        console.log('new message '+data['blog']);
        socket.emit(channel,data);
    });

    socket.on('disconnect',function () {
        redisClient.quit();
    });
});