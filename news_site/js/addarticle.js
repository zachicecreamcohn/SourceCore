let showRegBtn = document.getElementById('show-regular-article');
let showLinkBtn = document.getElementById('show-link-article');
let regArticleForm = document.getElementById('addregarticle');
let linkArticleForm = document.getElementById('addlinkarticle');

showRegBtn.addEventListener('click', function () {
    regArticleForm.style.display = 'block';
    linkArticleForm.style.display = 'none';
});

showLinkBtn.addEventListener('click', function () {
    regArticleForm.style.display = 'none';
    linkArticleForm.style.display = 'block';
});
