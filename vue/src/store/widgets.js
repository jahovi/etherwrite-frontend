/**
 * Widget configurations are stored here. For each widget, specify:
 *
 * - component: the vue component that is the actuall widget
 * - configuration: not yet in use
 * - moderatorWidget: if this is true, the widget will be shown in the catalog to moderators (=teachers) only. If this is false, the widget
 *      will be shown to normal users (=students) as well. This is not a security measure, but a way of preventing users from seeing
 *      widgets that wont show them anything because the backend doesn't provide data for users of their role.
 * - minW, minH: minimum widths and heigts in grid boxes
 *
 */

export default {
	state: {
		widgets: [
			{
				component: "authoringRatios_bar",
				category: "barchart",
				configuration: {},
				moderatorWidget: false,
				minW: 1,
				minH: 1,
			},
			{
				component: "authoringRatios_pie",
				category: "piechart",
				configuration: {},
				moderatorWidget: false,
				minW: 2,
				minH: 1,
			},
			{
				component: "participation_diagram",
				category: "barchart",
				configuration: {},
				moderatorWidget: true,
				minW: 3,
				minH: 1,
			},
			{
				component: "groupParticipants",
				category: "other",
				configuration: {},
				moderatorWidget: false,
				minW: 1,
				minH: 1,
			},
			{
				component: "operations_bar",
				category: "barchart",
				configuration: {},
				moderatorWidget: false,
				minW: 2,
				minH: 1,
			},
			{
				component: "activityOverTime",
				category: "linechart",
				configuration: {},
				moderatorWidget: false,
				minW: 3,
				minH: 1,
			},
			{
				component: "etherViz",
				category: "other",
				configuration: {},
				moderatorWidget: false,
				minW: 1,
				minH: 2,
			},
			{
				component: "cohesionDiagram",
				category: "other",
				configuration: {},
				moderatorWidget: false,
				minW: 2,
				minH: 2,
			},
			{
				component: "wsTestWidget",
				category: "other",
				configuration: {},
				moderatorWidget: false,
				minW: 1,
				minH: 1,
			},
			{
				component: "documentMetrics",
				category: "other",
				configuration: {},
				moderatorWidget: false,
				minW: 1,
				minH: 1,
			},

		],
	},
	getters: {
		getWidgets: function (state, getters, rootState) {
			// if user is moderator, return all widgets, else only none moderator widgets
			if (rootState.base.isModerator) {
				return state.widgets;
			} else {
				return state.widgets.filter(widget => widget.moderatorWidget === false);
			}
		},
		getWidgetCategories: function (state, getters, rootState) {
			let categories = [];
			// return widget categories based on user role
			if (rootState.base.isModerator) {
				state.widgets.forEach(widget => {
					if (!categories.includes(widget.category)) {
						categories.push(widget.category);
					}
				});
			} else {
				state.widgets.filter(widget => widget.moderatorWidget === false).forEach(widget => {

					if (!categories.includes(widget.category)) {
						categories.push(widget.category);
					}
				});
			}
			return categories;
		},
	},
};