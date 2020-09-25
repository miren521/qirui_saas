(function($,window,document,undefined){
    function Step (el,opts){
        this.$el=$(el),
        this.defaults={
            stepCount:3,
            stepTitles:['标题一','标题二','标题三'],
            stepContent:'.eis-stepContents',
            stepDirection:'x',
            showStepButton:false,
        }
        this.settings=$.extend({},this.defaults,opts);
    }
    Step.prototype={
        init:function(){
            var that = this,
                $stepBox=that.$el;
            // 初始化步骤条
            function _initStepBar(direct){
                var stepHtml=$('<div class="eis-form-steps"></div>'),
                    stepBarHtml ='';
                if(direct==='x'){
                    for(var i = 0;i<that.settings.stepCount;i++){
                        if(i===that.settings.stepCount-1){
                            stepBarHtml+='<div class="eis-form-step" style="width:'+(100/that.settings.stepCount).toFixed(2)+'%">\n' +
                                '            <div class="eis-step-head">\n' +
                                '                <div class="eis-step-icon">\n' +
                                '                    <div class="step-icon-txt">'+(i+1)+'</div>\n' +
                                '                </div>\n' +
                                '            </div>\n' +
                                '            <div class="eis-step-main">'+that.settings.stepTitles[i]+'</div>\n' +
                                '        </div>';
                        }else{
                            stepBarHtml+='<div class="eis-form-step" style="width:'+(100/that.settings.stepCount).toFixed(2)+'%">\n' +
                                '            <div class="eis-step-head">\n' +
                                '                <div class="eis-step-icon">\n' +
                                '                    <div class="step-icon-txt">'+(i+1)+'</div>\n' +
                                '                </div>\n' +
                                '                <div class="eis-step-line">\n' +
                                '                </div>\n' +
                                '            </div>\n' +
                                '            <div class="eis-step-main">'+that.settings.stepTitles[i]+'</div>\n' +
                                '        </div>';
                        }


                    }
                    stepHtml.append($(stepBarHtml));
                    stepHtml.append($('<div class="eis-step-progress"></div>'));
                    $stepBox.append(stepHtml);
                    // 步骤内容填充到dom中
                    var $stepContent = $stepBox.find(that.settings.stepContent);
                    $stepBox.append($stepContent);
                    $stepBox.find('.eis-stepContent').eq(0).show().siblings('.eis-stepContent').hide();
                    var $formStep = that.$el.find('.eis-form-steps'),
                        $formSteps = $formStep.find('.eis-form-step'),
                        $stepWidth = $formSteps.width(),
                        $stepProgress = $formStep.find('.eis-step-progress'),//步骤条
                        $stepIcon = $formSteps.find('.eis-step-icon');
                    $stepProgress.width($stepWidth/2);
                    $formSteps.eq(0).addClass('is-finish');
                    $stepIcon.each(function(index){
                        var $this = $(this),
                            len = $stepIcon.length;
                        $this.on('click',function(){
                            if(index===0){
                                $stepProgress.width($stepWidth/2);
                                $this.parent().parent().nextAll().removeClass('is-finish');
                                _initStepCon(index);
                                _initStepFooter(index);
                            }else if(index===$stepIcon.length-1){
                                $stepProgress.width($stepWidth*(that.settings.stepCount-1));
                                $this.parent().parent().addClass('is-finish');
                                $this.parent().parent().siblings().addClass('is-finish');
                                _initStepCon(index);
                                _initStepFooter(index);
                            }else{
                                $stepProgress.width($stepWidth/2+$stepWidth*index);
                                $this.parent().parent().addClass('is-finish');
                                $this.parent().parent().prevAll().addClass('is-finish');
                                $this.parent().parent().nextAll().removeClass('is-finish');
                                _initStepCon(index);
                                _initStepFooter(index);
                            }

                        })
                    })
                }else if(direct==='y'){
                    for(var i = 0;i<that.settings.stepCount;i++){
                        if(i===that.settings.stepCount-1){
                            stepBarHtml+='<div class="eis-form-step">\n' +
                                '            <div class="eis-step-head">\n' +
                                '                <div class="eis-step-icon">\n' +
                                '                    <div class="step-icon-txt">'+(i+1)+'</div>\n' +
                                '                </div>\n' +
                                '            </div>\n' +
                                '            <div class="eis-step-main">'+that.settings.stepTitles[i]+'</div>\n' +
                                '        </div>';
                        }else{
                            stepBarHtml+='<div class="eis-form-step">\n' +
                                '            <div class="eis-step-head">\n' +
                                '                <div class="eis-step-icon">\n' +
                                '                    <div class="step-icon-txt">'+(i+1)+'</div>\n' +
                                '                </div>\n' +
                                '                <div class="eis-step-line">\n' +
                                '                </div>\n' +
                                '            </div>\n' +
                                '            <div class="eis-step-main">'+that.settings.stepTitles[i]+'</div>\n' +
                                '        </div>';
                        }
                    }
                    stepHtml.append($(stepBarHtml));
                    stepHtml.append($('<div class="eis-step-progress"></div>'));
                    that.$el.append(stepHtml);
                    // 步骤内容填充到dom中
                    var $stepContent = $stepBox.find(that.settings.stepContent);
                    $stepBox.append($stepContent);
                    $stepBox.find('.eis-stepContent').eq(0).show().siblings('.eis-stepContent').hide();
                    $formStep = that.$el.find('.eis-form-steps');
                    $formSteps = $formStep.find('.eis-form-step');
                    $stepProgress = $formStep.find('.eis-step-progress');//步骤条
                    $stepIcon = $formSteps.find('.eis-step-icon');
                    var $stepHeight = $formSteps.height();
                    $stepProgress.height($stepHeight/2);
                    $formSteps.eq(0).addClass('is-finish');
                    $stepIcon.each(function(index){
                        var $this = $(this),
                            len = $stepIcon.length;
                        $this.on('click',function(){
                            if(index===0){
                                $stepProgress.height($stepHeight/2);
                                $this.parent().parent().nextAll().removeClass('is-finish');
                                _initStepCon(index);
                                _initStepFooter(index);
                            }else if(index===$stepIcon.length-1){
                                $stepProgress.height($stepHeight*(that.settings.stepCount-1));
                                $this.parent().parent().addClass('is-finish');
                                $this.parent().parent().siblings().addClass('is-finish');
                                _initStepCon(index);
                                _initStepFooter(index);
                            }else{
                                $stepProgress.height($stepHeight/2+$stepHeight*index);
                                $this.parent().parent().addClass('is-finish')
                                $this.parent().parent().nextAll().removeClass('is-finish');
                                _initStepCon(index)
                                _initStepFooter(index);
                            }

                        })
                    })
                }
                _initStepFooter(0)
            }
            // 处理步骤内容
            function _initStepCon(i){
                var $stepContent = that.$el.find(that.settings.stepContent),
                    $stepContents = $stepContent.find('.eis-stepContent');
                $stepContents.eq(i).show().siblings('.eis-stepContent').hide();
            }
            // 初始化生成上一步，下一步
            function _initStepFooter(i){
                if(that.settings.showStepButton===false)return;
                if($(that.settings.stepContent).find('.eis-stepFooter').length===0){
                    $(that.settings.stepContent).append('<div class="eis-stepFooter"></div>');
                }
                var $stepFooter = that.$el.find('.eis-stepFooter');
                $stepFooter.html('');
                if(i===0){
                    $stepFooter.append($('<button class="eis-btn eis-next-btn">下一步</button>'))
                }else if(i===that.settings.stepCount-1){
                    $stepFooter.append($(
                        '<button class="eis-btn eis-prev-btn">\n'+
                        '上一步</button>\n'+
                        '<button class="eis-btn eis-over-btn">完成</button>'))
                }else{
                    $stepFooter.append($(
                        '<button class="eis-btn eis-prev-btn">\n'+
                        '上一步</button>\n'+
                        '<button class="eis-btn eis-next-btn">\n'+
                        '下一步</button>'
                    ))
                }
                /*跳转上一步下一步begin*/
                var $stepContents = $stepBox.find('.eis-stepContent'),
                    $formSteps = $stepBox.find('.eis-form-steps'),
                    $stepIcon = $formSteps.find('.eis-step-icon');
                function _stepPrevMove(i){
                    i-=1;
                    if(i<0)i=0;
                    $stepIcon.eq(i).trigger('click')
                    _initStepCon(i);

                }
                function _stepNextMove(i){
                    i+=1;
                    if(i>=that.settings.stepCount-1)i=that.settings.stepCount-1;
                    $stepIcon.eq(i).trigger('click')
                    _initStepCon(i);
                }
                $stepContents.eq(i).parent().find('.eis-btn').on('click',function(){
                    var $this=$(this);
                    if($this.hasClass('eis-prev-btn')){
                        _stepPrevMove(i);
                    }else if($this.hasClass('eis-next-btn')){
                        _stepNextMove(i)
                    }
                })
                /*跳转上一步下一步end*/
            }
            if(that.settings.stepDirection==='x'){
                $stepBox.addClass('eis-horizontal-steps');
                _initStepBar('x');
            }else if(that.settings.stepDirection==='y'){
                $stepBox.addClass('eis-vertical-steps');
                _initStepBar('y');

            }



        }
    }
    $.fn.extend({
        step:function(opt){
            return this.each(function(){
                new Step($(this),opt).init()
            })
        }
    })
})(jQuery,window,document)