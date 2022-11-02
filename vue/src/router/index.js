import {createRouter, createWebHistory} from "vue-router";
import Editor from "../views/editor.vue";
import NotFound from "../views/notfound.vue";
import TeacherDashboard from "../views/teacherdashboard.vue";
import LearnerDashboard from "../views/learnerdashboard.vue";
import Task from "../views/task.vue";

/*
    @author Marc Burchart
    @copyright Marc Burchart, 2022, marc.burchart@fernuni-hagen.de
*/
// You have to use child routes if you use the same component. Otherwise the component"s beforeRouteUpdate
// will not be called.
const routes = [
	{path: "/", name: "task", component: Task},
	{path: "/editor", name: "editor", component: Editor},
	{path: "/teacherdashboard", name: "teacherdashboard", component: TeacherDashboard},
	{path: "/learnerdashboard", name: "learnerdashboard", component: LearnerDashboard},
	{path: "/:pathMatch(.*)*", name: "not found", component: NotFound},
];

// base URL is /mod/vuejsdemo/view.php/[course module id]/
const currenturl = window.location.pathname;
const base = currenturl.substring(0, currenturl.indexOf(".php")) + ".php/" + 2 + "/";

export default createRouter({
	history: createWebHistory(base),
	routes,
	base,
});