var app = require('http').createServer(handler);
var io = require('socket.io')(app);
var url = require('url');

app.listen(443);

function handler (req, res) {
    var params = url.parse(req.url, true).query;
    if(params.qrcode){
        try{
            if(qrcode_list[params.qrcode]){
                qrcode_list[params.qrcode].emit('message', {ret_code: 0, ret_msg: params.msg});
            }
        } catch(e){

        }
        
    }
    res.end();
}

var qrcode_list = {};

io.on('connection', function (socket) {
    socket.on('join', function(data){
        qrcode_list[data.qrcode] = socket;
    });
    
    socket.on('disconnect',function(){
        for(var i in qrcode_list){
            if(qrcode_list[i].id == socket.id){
                delete qrcode_list[i];
                break;
            }
        }
    });
});