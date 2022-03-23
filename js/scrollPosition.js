// if get request 'scroll' is set, update the scroll position
if (getRequest('scroll')) {
    document.getElementsByClassName('comment-form')[0].scrollIntoView({
        behavior: 'auto'
    });
}