jQuery(document).ready(function($){$("#post-submission-form").on("submit",function(e){e.preventDefault();var o=$("#post-submission-title").val(),t=$("#post-submission-excerpt").val(),s=$("#post-submission-content").val(),n="published",i={title:o,excerpt:t,content:s};$.ajax({method:"POST",url:POST_SUBMITTER.root+"wp/v2/posts",data:i,beforeSend:function(e){e.setRequestHeader("X-WP-Nonce",POST_SUBMITTER.nonce)},success:function(e){console.log(e),alert(POST_SUBMITTER.success)},fail:function(e){console.log(e),alert(POST_SUBMITTER.failure)}})})});