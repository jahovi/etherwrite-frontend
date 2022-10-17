import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Notification from 'core/notification';

/* 
    @author Marc Burchart
    @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de
*/

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        courseModuleID: 0,
        contextID: 0,
        strings: {},
        alertShow: false,
        alertType: "alert-primary",
        alertMessage: ""
    },
    //strict: process.env.NODE_ENV !== 'production',
    getters: {       
        getStrings: function(state){            
            return state.strings;
        },
        getCMID: function(state){
            return state.courseModuleID;
        },
        getAlertShow: function(state){            
            return state.alertShow;
        },
        getAlertType: function(state){
            return state.alertType;
        },
        getAlertMessage: function(state){
            return state.alertMessage;
        }
    },
    mutations: {
        setCourseModuleID: function(state, id) {
            state.courseModuleID = id;
        },
        setContextID: function(state, id) {
            state.contextID = id;
        },
        setStrings: function(state, strings) {            
            state.strings = strings;
        },
        setAlertPermanent: function(state, arr){    
            scroll(0,0);        
            state.alertShow = true;
            state.alertType = arr[0];
            state.alertMessage = arr[1];
        },
        setAlertWithTimeout: function(state, arr){
            scroll(0,0);
            state.alertShow = true;
            state.alertType = arr[0];
            state.alertMessage = arr[1];
            new Promise(
                resolve => setTimeout(resolve, arr[2])
            ).then(
                () => {
                    state.alertShow = false;
                    state.alertType = "alert-primary";
                    state.alertMessage = "";
                }
            );
        },       
        removeAlert: function (state){
            state.alertShow = false;
            state.alertType = "alert-primary";
            state.alertMessage = "";
        }
    },
    actions: {
        async loadComponentStrings(context) {
            const lang = $('html').attr('lang').replace(/-/g, '_');
            const cacheKey = 'mod_write/strings/' + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                context.commit('setStrings', JSON.parse(cachedStrings));
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': 'mod_write',
                        lang,
                    },
                };
                const loadedStrings = await moodleAjax.call([request])[0];
                let strings = {};
                loadedStrings.forEach((s) => {
                    strings[s.stringid] = s.string;
                });
                context.commit('setStrings', strings);
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            }
        }
    }
});
