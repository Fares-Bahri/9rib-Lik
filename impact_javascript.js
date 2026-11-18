document.getElementById('id_date_signalement').value=new Date();

document.getElementById('id_date_impact').value=new Date();

const today=new Date();
document.getElementById('id_date_signalement').value=today;
document.getElementById('id_date_limite').min=today;

document.querySelectorAll('.search').forEach(function(box){
  box.addEventListener('input',function(){
    var q=box.value.toLowerCase();
    box.closest('.card').querySelectorAll('tbody tr').forEach(function(tr){
      tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none';
    });
  });
});