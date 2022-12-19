/**
 * Widget configurations are stored here. For each widget, specify:
 *
 * - component: the vue component that is the actuall widget
 * - configuration: not yet in use
 * - moderatorWidget: if this is true, the widget will be accessible to moderators (=teachers) only. If this is false, the widget
 *      will be accessible to normal users (=students) as well. Widgets that handle role distinction internally should state false.
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
				minW: 2,
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
						let category = widget.category.charAt(0).toUpperCase() + widget.category.slice(1);
						categories.push(category);
					}
				});
			} else {
				state.widgets.filter(widget => widget.moderatorWidget === false).forEach(widget => {
					if (!categories.includes(widget.category)) {
						let category = widget.category.charAt(0).toUpperCase() + widget.category.slice(1);
						categories.push(category);
					}
				});
			}
			return categories;
		},
	},
};