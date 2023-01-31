/**
 * Widget configurations are stored here. For each widget, specify:
 *
 * - component: the vue component that is the actuall widget
 * - configuration: not yet in use
 * - moderatorWidget: if this is true, the widget will be shown in the catalog to moderators (=teachers) only. If this is false, the widget
 *      will be shown to normal users (=students) as well. This is not a security measure, but a way of preventing users from seeing
 *      widgets that wont show them anything because the backend doesn't provide data for users of their role.
 * - defaultWidth, defaultHeight: default widths and heigts in dashboard grid boxes when the widget is added from the catalog
 *
 */

export default {
	state: {
		widgets: [
			{
				component: "authoringRatios_bar",
				categories: ["activity"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 7,
				defaultHeight: 12,
			},
			{
				component: "authoringRatios_pie",
				categories: ["activity"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 6,
				defaultHeight: 10,
			},
			{
				component: "participation_diagram",
				categories: ["collaboration", "timecourse"],
				configuration: {},
				moderatorWidget: true,
				defaultWidth: 12,
				defaultHeight: 10,
			},
			{
				component: "operations_bar",
				categories: ["activity"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 10,
				defaultHeight: 14,
			},
			{
				component: "activityOverTime",
				categories: ["timecourse", "activity"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 12,
				defaultHeight: 10,
			},
			{
				component: "etherViz",
				categories: ["timecourse", "activity"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 8,
				defaultHeight: 17,
			},
			{
				component: "cohesionDiagram",
				categories: ["collaboration"],
				configuration: {},
				moderatorWidget: false,
				defaultWidth: 4,
				defaultHeight: 17,
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
			// return widget categories based on user role
			let widgets;
			if (rootState.base.isModerator) {
				widgets = state.widgets;
			} else {
				widgets = state.widgets
						.filter(widget => widget.moderatorWidget === false);
			}

			const categories = [];
			widgets.forEach(widget => {
				widget.categories.forEach(category => {
					if (!categories.includes(category)) {
						categories.push(category);
					}
				});
			});
			return categories;
		},
	},
};