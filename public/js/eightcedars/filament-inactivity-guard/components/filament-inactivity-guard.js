function a(t,i,e,o){return{inactivityTimer:null,logoutTimeout:null,init(){this.resetInactivityTimer(),i.forEach(s=>{window.addEventListener(s,()=>this.resetInactivityTimer())}),window.addEventListener("resumeActivities",()=>this.resumeActivities())},resetInactivityTimer(){clearTimeout(this.inactivityTimer),clearTimeout(this.logoutTimeout),this.inactivityTimer=setTimeout(()=>{this.showInactivityModal()},e)},showInactivityModal(){if(!this.logoutTimeout<1){t.$call("logout");return}this.$dispatch("open-modal",{id:"inactivityModal"}),this.$dispatch("start-logout-countdown"),this.logoutTimeout=setTimeout(()=>{t.$call("logout")},o)},resumeActivities(){this.$dispatch("close-modal",{id:"inactivityModal"}),this.resetInactivityTimer()}}}export{a as default};
