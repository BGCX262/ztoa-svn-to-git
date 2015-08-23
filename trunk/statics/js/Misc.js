/**
 * Created with JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-6-8
 * Time: 上午11:06
 * To change this template use File | Settings | File Templates.
 */

function Misc(){

};

Misc.unique = function(){
    var unique = 'u' + parseInt(new Date().getTime() * Math.random());

    return unique;
};
