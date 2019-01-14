
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// some routes and etc sharing by main layout
let captchaInput = document.querySelector('input[name="captcha[input]"]');
captchaInput.setAttribute('v-model',"commentForm.data.captcha.input");


Vue.prototype.$http = axios;


const vm = new Vue({
  el: '#app',
  data:{
    commentForm:{
      data:{
        username:'',
        email:'',
        homepage:'',
        content:'',
        attachment:'',
        captcha:{
          id:'',
          input:''
        },
        parent_id:0,
        post_id:0
      },
      errors: {},
      isSending:false,
      success:false
    },
    file:{
      extension:null,
      src:null,
      allowableExtns:['txt','png','jpg','jpeg','gif'],
    },
    lightbox:{
      src:null,
      type:''
    },
    pagination:{
      lastPage:'',
      perPage:'',
      currentPage: '',
      totalCommentsOnPage:'',
      totalItemCount:''
    },
    previewExists:false,
    isLightboxShowed: false,
    isLightboxLoading: false,
  },
  methods: {
    sendComment(e) {
      let url = e.target.action;
      let formData = new FormData();
      //set formdata
      for(let key in this.commentForm.data) {
        if (key == 'captcha') {
          //will need parse on server to array
          formData.set('captcha',this.commentForm.data[key].id+'||'+this.commentForm.data[key].input);
          continue;
        }
        formData.set(key, this.commentForm.data[key]);
      }
      this.commentForm.isSending = true;
      this.$http.post(
              url,
              formData,
              {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
               }
      ).then(resp => {
        console.log(resp);
        if(resp.data.status == 'error') {
          this.commentForm.errors = resp.data.errors;
          console.log( this.commentForm.errors);
        } else if (resp.data.status == 'ok') {
          this.commentForm.errors = {};
          this.commentForm.success = true;
          setTimeout(() => this.commentForm.success = false, 5000);
          this.addCommentToTree(resp.data);

        }
      }).catch( err => {
            console.error(err);
       })
       .finally(() => {
            this.commentForm.isSending = false;
        });
    },

    handleFileUpload(e) {
      this.commentForm.data.attachment = this.$refs.attachment.files[0];
      console.log( this.$refs.attachment.files[0]);
      this.file.extension = this.commentForm.data.attachment.name.split('.').pop();
      if(!~this.file.allowableExtns.indexOf(this.file.extension.toLowerCase())) {
        Vue.set(this.commentForm.errors, 'attachment', {wrongExtension:'Not allowable extension'});
      } else {
        Vue.delete(this.commentForm.errors, 'attachment');
      }


      // RETRIEVE SRC, THAT WILL BE USED IN PREVIEW
        let reader = new FileReader();
        let src;
        reader.onload = () => {
          this.file.src = reader.result;
        };
        reader.onerror = function (e) {
          console.error('FIle read has fall')
        };
      if(this.file.extension != 'txt') {
        reader.readAsDataURL(this.commentForm.data.attachment);
      } else {
        reader.readAsText(this.commentForm.data.attachment);
      }
    },
    delegateClick(e){
      console.log('delegate',e.target);
      let elem = e.target;
      if(elem.matches('.button_reply')) {
        this.reply(elem);
      } else if (elem.matches('.attachment')) {
        this.showLightbox(elem);
      }

    },
    showLightbox(elem) {

      let body = document.body;
      body.style.paddingRight = (window.innerWidth - body.clientWidth) + 'px';
      document.documentElement.style.overflow = 'hidden';
      let type = elem.dataset.type;

      this.isLightboxShowed = true;
      this.lightbox.type = type;
      switch(type) {
        case 'txt':
          axios.get(elem.dataset.src)
              .then(resp => {
                console.log(resp);
                this.isLightboxLoading = false;
                this.lightbox.src = resp.data;
              })
              .catch(e => console.error(e));

          break;
        case 'img':
          this.lightbox.src = elem.src;
          this.isLightboxLoading = false;
          break;
        case 'inlineTxt':
            this.lightbox.src = elem.dataset.src;
            this.isLightboxLoading = false;
          break;
      }
    },
    lightboxUnset() {
      document.body.style.paddingRight = '';
      document.documentElement.style.overflow = '';
      this.isLightboxShowed = false;
      this.isLightboxLoading = false;
      this.lightbox.src = null;
    },
    reply(elem) {
      console.log(elem);
      elem = elem.closest('.comment-list__item');
      if(!elem) return;

      this.commentForm.data.parent_id = elem.dataset.id;
      let list = elem.querySelector('.comment-list');
      if(list) {
        elem.insertBefore(this.$refs.commentForm, list);
      } else {
        elem.appendChild(this.$refs.commentForm);
      }
    },
    addCommentToTree(respData) {

      let url = new URL(location.href);
      let order = url.searchParams.get("order") || '';
       // redirect if comment does not depend to current page
        if(!this.commentForm.data.parent_id
            && order == 'asc'
            && (this.pagination.currentPage < this.pagination.lastPage
            || this.pagination.perPage <= this.pagination.totalCommentsOnPage)) {
          let lastPage = Math.ceil((this.pagination.totalItemCount + 1) / this.pagination.perPage);
          let href = `?page=${lastPage}&order=asc&scrollTo=toLastComment`;
          window.location.href = href;
        }


      // we have some text in attributes so we dont need to load it from server
      let type = (respData.attachment.type == 'txt') ? 'inlineTxt' : respData.attachment.type;
      let params = Object.assign(
          {},
          this.commentForm.data,
          {
            extension: respData.attachment.type,
            src: this.file.src,
            id: respData.attachment.id
          }
      );

      let appendTarget = this.getAppendTarget();
      let elem = this.getNewCommEl(params);

      // if ascending order, need to insert
      if(order == 'desc' && !this.commentForm.data.parent_id) {
        appendTarget.insertAdjacentElement('afterbegin', elem);
      } else {
        appendTarget.appendChild(elem);
      }
      elem.scrollIntoView();
    },
    preview() {
      if (this.previewExists || !this.file.extension) {
        Vue.set(this.commentForm.errors, 'attachment', {wrongExtension:'File is required'});
        return;
      }

      let url = new URL(location.href);
      let order = url.searchParams.get("order") || '';
      let params = Object.assign(
            {},
            this.commentForm.data,
            {
              extension: this.file.extension,
              src: this.file.src
            }
          );
      let appendTarget = this.getAppendTarget();
      let elem = this.getNewCommEl(params);
      let replyBtn = elem.querySelector('button');
      replyBtn.classList.remove('button_reply');

      if(order == 'desc' && !this.commentForm.data.parent_id) {
        appendTarget.insertAdjacentElement('afterbegin', elem);
      } else {
        appendTarget.appendChild(elem);
      }
      elem.scrollIntoView();
      this.previewExists = true;
      setTimeout(() => {
        this.previewExists = false;
            elem.parentNode.removeChild(elem);
          }
        , 5000
      );
    },
    showFile(elem) {
    },
    addTagToText(e) {
      if(!e.target.matches('.controls__button')) {
        return;
      }
      let tags = {
        i:'<i></i>',
        code:'<code></code>',
        a:'<a href="" title=""></a>',
        strong:'<strong></strong>',
      };
      let tag = tags[e.target.dataset.tag];
      if(!tag) {
        return;
      }
      let textarea = this.$refs.textarea;
      this.commentForm.data.content += tag;

    },
    // get target to add comment
    getAppendTarget() {
      let appendTarget;
      let parentId = this.commentForm.data.parent_id;
      if(parentId) {
        let parent = document.querySelector(`.comment-list__item[data-id="${parentId}"`);
        console.log(parent);
        let childCommentsContainer = parent.querySelector('.comment-list');
        if(!childCommentsContainer) {
          childCommentsContainer = document.createElement('div');
          childCommentsContainer.classList.add('comment-list');
        }

        if(parent.contains(this.$refs.commentForm)) {
          parent.insertBefore(childCommentsContainer, this.$refs.commentForm);
          appendTarget = childCommentsContainer;
        } else {
          parent.appendChild(childCommentsContainer);
          appendTarget = childCommentsContainer;

        }
      } else {
        appendTarget = document.querySelector('.post__comments > .comment-list');
      }

      return appendTarget;
    },
    getNewCommEl(params) {
      this.$refs.clrComm.dataset.id = params.id || 0;
      this.$refs.clrCommUsername.textContent = params.username;
      this.$refs.clrCommEmail.textContent = params.email;
      this.$refs.clrCommCreatedAt.textContent = (new Date()).toISOString().replace(/\..*$/, '').replace('T',' ');
      this.$refs.clrCommContent.innerHTML = params.content + this.getAttachmentHtml(params.src, params.extension);
      if(params.homepage) {
        this.$refs.clrCommHomepage.href = params.homepage;
        this.$refs.clrCommHomepage.hidden = '';
      }

      let clone = this.$refs.clrComm.cloneNode(true);
      clone.hidden = '';
      return clone;
    },
    getAttachmentHtml(src, extension) {
      if(extension == 'txt') {
        src = src.replace(/"/g, "&quot;").replace(/\\/g,"");
        //inline because using in lightbox in another form
        return  `<div data-type="inlineTxt" data-src="${src}" class="attachment attachment_txt"></div>`;
      }
      return `<img data-type="img" src="${src}" class='attachment attachment_image'>`;
    },
    unsetReply() {
      this.commentForm.data.parent_id = 0;
      let formOriginParent = document.querySelector('.form-origin');
      formOriginParent.appendChild(this.$refs.commentForm);
    },
    paginationSet() {
      console.log(this.$refs.sortForm);
      this.pagination.perPage = this.$refs.sortForm.dataset.perpage;
      this.pagination.currentPage = this.$refs.sortForm.dataset.currentpage;
      this.pagination.lastPage = this.$refs.sortForm.dataset.lastpage;
      this.pagination.totalItemCount = this.$refs.sortForm.dataset.totalitemcount;
      this.pagination.totalCommentsOnPage =
          document.querySelectorAll('.post__comments > .comment-list > .comment-list__item').length;
    }

  },

  mounted() {
    let captchaId = document.querySelector('input[name="captcha[id]"]');
    this.commentForm.data.captcha.id = captchaId.value;
    this.commentForm.data.post_id = this.$refs.post_id.value;
    this.paginationSet();

    let url = new URL(location.href);
    if(url.searchParams.get('scrollTo') == 'toLastComment') {
      let elem = document.querySelector('.post__comments > .comment-list > .comment-list__item:last-child');
      if(elem) {
        elem.scrollIntoView(true);
      }
    }

  }
});