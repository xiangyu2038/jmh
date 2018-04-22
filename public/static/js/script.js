// 协助获取焦点
$('.list li').click(function(){
    // 文本域\输入框获得焦点
    if($(this).find('textarea').length) $(this).find('textarea').focus();
    if($(this).find('input').length) $(this).find('input').focus();
});
// 点亮评价星星
$('.star .icon-star').click(function(){
    $(this).addClass('on');
    $(this).prevAll().addClass('on');
    $(this).nextAll().removeClass('on');
});

// 状态提示页面
function prompts(type,tit,info,num,url){
    $('.wrap').load(url,function(){
        $('.prompt .tit').text(tit);
        $('.prompt .info').text(info);
        $('.prompt .'+type).show();
        if(num){
            $('.prompt .num').text(num);
        };
    });
};

// 图片存储容器
var imgArr=[];
var ajaxState=false;
// 上传接口
function Ajax(url,type){
    //alert('准备数据');
    if(ajaxState) return;
    ajaxState=true;
    var data={};
    if(type=='eva'){
        data.satisfied=$('.satisfied i.on').length;
        data.convenient=$('.convenient i.on').length;
        data.clean=$('.clean i.on').length;
        data.clear=$('.clear i.on').length;
        data.attitude=$('.attitude i.on').length;
        data.openid=$('.openid').val();
        data.repair_id=$('.repair_id').val();
        data.name=$('.name').val();
    }else{
        $('input').each(function(){
            var key=$(this).attr('name');
            data[key]=$(this).val();
        });
        if($('textarea').length) data[$('textarea').attr('name')]=$('textarea').val();
        if(type=='img'){
            if(imgArr.length==0){
                ajaxState=false;
                return alert('请上传图片!');
            }else{
                data.serverId=imgArr;
            }
        }
        if($('select').length){
            $('select').each(function(){
                var key=$(this).attr('name');
                data[key]=$(this).val();
            });
        };
        if(type=='reservation'){
            
        }
    }
    // alert('上传数据');
    $.ajax({
        type:'post',
        url:url,
        data:data,
        success:function(result){
            ajaxState=false;
            if(result.error_code==1 && $('.prompt').length) return $('.prompt').show();
            alert(result.msg);
        },
        error:function(){
            ajaxState=false;
            alert('服务器异常，请稍后再试！');
        }
    });
};

// 选择房产
$('[name=project_id]').change(function(){
    proJect($(this).find("option:selected").text());
});

function proJect(txt){
    $('.project').text(txt);
};
proJect($('[name=project_id]').find("option:selected").text());





