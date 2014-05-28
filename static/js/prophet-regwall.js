/**
 * Created by mmaedler on 19.05.14.
 */

// show regwall
jQuery(document).ready(function() {
    if (pbs_regwall_read_cookie("regflag") == "ok") return;
    jQuery.each(["pbs_regwall_obscured","pbs_regwall_registration_overlay","pbs_regwall_registration_block"], function (idx, id) {
        jQuery("#"+id).addClass(id);
    });
});


function pbs_regwall_is_valid_email (email) {
    var filter = /^([a-zA-Z0-9]+[a-zA-Z0-9_\-\.]*\@([a-zA-Z0-9]+[a-zA-Z0-9\_-]*\.)+[a-zA-Z0-9]+)$/;
    return filter.test(email) ? true : false;
}

function pbs_regwall_check_form (f) {
    f = jQuery(f);
    if (! f) { return true; }
    var missing = "";
    f.find("label.pbs_regwall_required").each(function(idx,el){
        el = jQuery(el);
        el.removeClass("pbs_regwall_missing");
        var field = el.attr("for") ? el.attr("for") : el.attr("htmlFor");
        field = jQuery("#"+field);
        if (! field) { return; }
        if (jQuery.trim(field.val()).length == 0) {
            var name = el.text().replace(/:$/, "");
            missing += "Please provide input for \"" + name + "\"\n";
            el.addClass("pbs_regwall_missing");
        } else if (field.attr("name").match(/email/) && ! pbs_regwall_is_valid_email(field.val())) {
            var name = el.text().replace(/:$/, "");
            missing += "Please provide a valid entry for \"" + name + "\"\n";
            el.addClass("pbs_regwall_missing");
        }
    });
    if (missing != "") {
        alert(missing);
        return false;
    }
    return true;
}

var etrHead=document.getElementsByTagName("head")[0];var subBut=document.getElementById("etrSubmit");etrLoadScript("http://trk.etrigue.com/etrigueForm.js"); function etrLoadScript(location){var script=document.createElement("script");script.src=location;script.type="text/javascript";etrHead.appendChild(script);}
function pbs_regwall_checkData(){ if (! pbs_regwall_check_form("#etrReg")) { return; } var etrigueForm=new EtrigueForm(1023);subBut.disabled=true;subBut.value="Please wait...";etrigueFormSuccess=false;etrigueForm.submitClassic("etrReg",function(dat){if(dat.err){subBut.disabled=false;subBut="Submit";return;}etrigueFormSuccess=true;pbs_regwall_registered();if(dat.thankYouPage){if(dat.repost){document.etrReg.action=dat.thankYouPage;document.etrReg.submit();}else{window.location=dat.thankYouPage;}}}); }
function pbs_regwall_registered () {
    // remember data for other locked articles
    pbs_regwall_set_cookie("regflag", "ok", 365);

    // make a note to GA
    _gaq = _gaq || [];
    _gaq.push(["_trackEvent", "ArticleSignUp", "Submit", jQuery("#pbs_regwall_trackingfield").val()]);

    // reveal the post
    jQuery.each(["pbs_regwall_obscured","pbs_regwall_registration_overlay","pbs_regwall_registration_block"], function (idx, id) {
        jQuery("#"+id).removeClass(id);
    });
}
function pbs_regwall_set_cookie (name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

// credits to http://stackoverflow.com/questions/5639346/shortest-function-for-reading-a-cookie-in-javascript
function pbs_regwall_read_cookie (key) {
    var result;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? (result[1]) : null;
}