var app = require('http').createServer(handler);
var io = require('socket.io')(app);

var Redis = require('ioredis');
var redis = new Redis();

app.listen(3000, function () {
    console.log('server running ');
});

function handler(req, res) {
    res.writeHead(200);
    res.end('');
}

io.on('connection', function (socket) {
    
});

redis.psubscribe('create:blog',function (err, count) {
    
});

redis.on('create:blog', function(subscribe, channel, message){
    message = JSON.parse(message);
    console.log(channel+':'+message.event);
    io.emit(channel, message.data);
})