/**
 * jQuery FB calendar box
 * 
 */
(function($)
{
    var inputBox = null;
    var opts = {
        'yearSel':  null,
        'monthSel': null,
        'daySel':   null,
        'hourSel':  null,
        'minSel':   null,
        'yearStart':null,
        'yearEnd':  null
    };
    /**
     * main gmailname function
     */
    $.fn.hfbCalendar = function(options)
    {
        //create options
        opts = $.extend({}, opts, options);

        // return the object back to the chained call flow
        return this.each(function()
        {
            inputBox = $(this);
            var dates = strToDate(inputBox.val());
            if(dates == null){
                dates = {year:null,month:null,day:null,hour:null,min:null};
            }
            renderYears(dates.year);
            renderMonths(dates.month);
            renderDays(dates.day, dates.month, dates.year);
            renderHours(dates.hour);
            renderMins(dates.min);

            if(options && options.yearSel){
                $(options.yearSel).bind('change', yearMonthChange).bind('change', elementChange);
            }
            if(options && options.monthSel){
                $(options.monthSel).bind('change', yearMonthChange).bind('change', elementChange);
            }
            if(options && options.daySel){
                $(options.daySel).bind('change', elementChange);
            }
            if(options && options.hourSel){
                $(options.hourSel).bind('change', elementChange);
            }
            if(options && options.minSel){
                $(options.minSel).bind('change', elementChange);
            }
        });
    };
    

    function elementChange(){
        inputBox.val(dateToStr());
    }

    function yearMonthChange(){
        renderDays($(opts['daySel']).val(), $(opts['monthSel']).val(), $(opts['yearSel']).val());
    }

    function daysInMonth(month, year){
        return 32 - new Date(year, month - 1, 32).getDate();
    }

    function renderYears(selectedYear){
        var d = new Date();
        var startYear = parseInt(d.getFullYear());
        if(opts.yearStart){
            startYear = parseInt(opts.yearStart);
        }
        var endYear = startYear + 10;
        if(opts.yearEnd){
            endYear = parseInt(opts.yearEnd);
        }
        var html = '<option value="">YYYY</option>';
        for(var i = startYear; i <= endYear; i++){
            html += '<option value="' + i + '">' + i + '</option>';
        }
        if(opts['yearSel']){
            $(opts['yearSel']).html(html);
        }
        if(selectedYear){
            $(opts['yearSel']).val(selectedYear);
        }
    }

    function renderMonths(selectedMonth){
        var html = '<option value="">MM</option>';
        for(var i = 1; i <= 12; i++){
            html += '<option value="' + i + '">' + format00(i) + '</option>';
        }
        if(opts['monthSel']){
            $(opts['monthSel']).html(html);
        }
        if(selectedMonth){
            $(opts['monthSel']).val(selectedMonth);
        }
    }

    function renderDays(selectedDay, selectedMonth, selectedYear){
        var lastDay = 31;
        if(selectedMonth && selectedYear){
            lastDay = daysInMonth(selectedMonth, selectedYear);
        }
        var html = '<option value="">DD</option>';
        for(var i = 1; i <= lastDay; i++){
            html += '<option value="' + i + '">' + format00(i) + '</option>';
        }
        if(opts['daySel']){
            $(opts['daySel']).html(html);
        }
        if(selectedDay){
            $(opts['daySel']).val(selectedDay);
        }
    }

    function renderHours(selectedHour){
        var html = '<option value="">HH</option>';
        for(var i = 0; i <= 23; i++){
            html += '<option value="' + i + '">' + format00(i) + '</option>';
        }
        if(opts['hourSel']){
            $(opts['hourSel']).html(html);
        }
        if(selectedHour){
            $(opts['hourSel']).val(selectedHour);
        }
    }

    function renderMins(selectedMin){
        var html = '<option value="">MM</option>';
        for(var i = 0; i <= 59; i++){
            html += '<option value="' + i + '">' + format00(i) + '</option>';
        }
        if(opts['minSel']){
            $(opts['minSel']).html(html);
        }
        if(selectedMin){
            $(opts['minSel']).val(selectedMin);
        }
    }

    function format00(val){
        if(val < 10){
            return '0' + val;
        }
        else{
            return val;
        }
    }

    function dateToStr(){
        var res = "";
        
        if( $(opts['yearSel']).val() == '' &&
            $(opts['monthSel']).val() == '' &&
            $(opts['daySel']).val() == ''){
            return "";
        }
        
        if( $(opts['yearSel']).val() == '' ||
            $(opts['monthSel']).val() == '' ||
            $(opts['daySel']).val() == ''){
            return false;
        }

        res =   $(opts['yearSel']).val() + '-' +
                format00($(opts['monthSel']).val()) + '-' +
                format00($(opts['daySel']).val());

        if( $(opts['hourSel']).val() == '' ||
            $(opts['minSel']).val() == ''){
            return res;
        }

        res += ' ' + format00($(opts['hourSel']).val()) +
               ':' + format00($(opts['minSel']).val()) + ':00';
        return res;
    }

    function strToDate(str){
        var match = /^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})$/.exec(str);
        if(match != null){
            return {
                year: parseInt(match[1],10),
                month: parseInt(match[2],10),
                day: parseInt(match[3],10),
                hour: parseInt(match[4],10),
                min: parseInt(match[5],10)
            };
        }
        var match = /^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/.exec(str);
        if(match != null){
            return {
                year: parseInt(match[1],10),
                month: parseInt(match[2],10),
                day: parseInt(match[3],10)
            };
        }
        return null;
    }

    /**
     * running options
     */
    opts = {};
})(jQuery); // pass the jQuery object to this function