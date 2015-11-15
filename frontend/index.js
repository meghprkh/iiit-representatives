var bread = [];
var baseurl = 'http://localhost/~m23/backend/index.php/';
var oldid = 0;

function goto(id) {
  $.ajax(baseurl + 'position/' + oldid)
    .done(function (data) {
      bread.push(data);
      $.ajax(baseurl + 'children/' + id)
        .done(function (data) {
          oldid = id;
          console.log(data);
          console.log(bread);
          // breadupdate();
          makeCards(data);
        });
    });
}

function breadupdate() {
  var str="";
  for (var i = 0; i < bread.length; i++) {
    str += '<li><a href=\"#';
    str += bread[i].id;
    str += '\" onclick=\"bread.slice(0,' + i + ')\">' + bread[i].title + '</a></li>';
  }
  $('#breadcrumb').html(str);
}

function makeCards (data) {
  var str = '';
  for (var i = 0; i < data.length; i++) str += makeCard(data[i]);
  $('#mainCont').html(str);
}

function makeCard (data) {
  var x = '';
  var xc = '';
  var classx="";
  var y = '';
  console.log(data);
  if (data.held_by == '0') {
    x = '<a href="#' + data.child_id + '"">';
    xc='</a>';
    classx="dep";
  }
  else y =  'onclick="$(this).toggleClass(\'hover\')"'
  console.log(data.child_id);
  return '<div class="pure-u-1-4">\
  <div class="flip-container" ' + y + '>'
    + x +
    '<div class="flipper ' + classx +'">\
      <div class="front">'
       +  data.title +
      '</div>\
      <div class="back">'
      + data.name+'<br>'+data.email+'<br>'+data.phone +
      '</div>\
    </div>'+xc+
  '</div>\
  </div>';
}

function toggleCard(el) {
  console.log('hi');
  $(el).toggleClass('hover')
}

function invalidHash() {
  if (isNaN(parseInt(window.location.hash.substr(1)))) window.location.hash='0';
}

$(window).bind('hashchange', function(e) {
  invalidHash();
  goto(window.location.hash.substr(1));
});

$(window).ready(invalidHash);
