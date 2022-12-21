<!-- eslint-disable vue/multi-word-component-names -->
<!-- @author Marie Freise -->
<!-- @copyright Marie Freise, 2022, marie_freise@web.de -->

<template>
	<div>
		<div class="scrollbox scrollbox_delayed" v-dragscroll.y="true">
			<div class="minimap" ref="minimapRef">
				<div class="outerContainer">
					<div class="scrollViewContainer">
						<div
								class="scrollView"
								v-for="userPos in userPositions"
								:key="userPos.id"
								:style="{
                background: userPos.color,
                top: userPos.top + 'px',
              }"
						></div>
					</div>
					<div class="textBlockContainer" ref="textBlocks">
            <span
								:class="block.headingType"
								v-for="block in coloredBlocks"
								:key="block.id"
								class="textBlock"
								:style="{
                background: block.color,
                color:
                  block.headingType === 'h1' && block.color === 'transparent'
                    ? 'rgb(100,210,155)'
                    : 'black',
              }"
						>{{ block.content }}</span
						>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { dragscroll } from "vue-dragscroll";
import Communication from "../classes/communication";

export default {
	data: function () {
		return {
			keepRefreshing: true,
			coloredBlocks: [],
			authorData: {},
			blockInfo: [],
			scrollPos: {},
			userPositions: [],
		};
	},
	directives: {
		dragscroll,
	},
	props: {},
	computed: {
		padName() {
			return this.$store.state.base.padName;
		},
	},
	mounted() {
		Communication.getFromEVA("minimap/authorInfo")
				.then(authors => this.authorData = authors);
		this.refreshMinimap();
	},
	methods: {
		refreshMinimap: async function () {

			while (this.keepRefreshing) {

				await Communication.getFromEVA("minimap/authorInfo")
						.then(authors => this.authorData = authors);

				Communication.getFromEVA("minimap/blockInfo", {
					padName: this.padName,
				}).then(blockInfo => this.blockInfo = blockInfo);

				Communication.getFromEVA("minimap/scrollPositions", {
					padName: this.padName,
				}).then(scrollPos => this.scrollPos = scrollPos);

				this.processTextBlocks();
				this.processScrollPos();

				await this.sleep(2000);
			}
		},
		processScrollPos: function () {

			this.userPositions = [];

			for (const [authorId, scrollPosInfo] of Object.entries(this.scrollPos)) {
				const textBlocks = this.$refs.textBlocks.children;

				if (textBlocks.length > scrollPosInfo.topIndex - 1) {
					const authorColor = this.authorData[authorId]["color"];

					const minimapRef = this.$refs.minimapRef;

					const topPosition = textBlocks[scrollPosInfo.topIndex - 1]
							.getBoundingClientRect().top - minimapRef.getBoundingClientRect().top; //Use the top of the minimap as an anchor reference point to position the viewport of the users

					this.userPositions.push({id: this.userPositions.length, top: topPosition, color: authorColor});
				}
			}
		},
		processTextBlocks: function () {

			this.coloredBlocks = [];
			let isHeading = false;
			let currentHeadingType = "normal";
			const sampleText = "Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet";

			this.blockInfo.forEach((block) => {
				let rndContent = "";
				const blockColor = block.ignoreColor ? "transparent" : this.authorData[block.author]["color"]; //Getting the author's color for the current block
				let sampleIndex = 0;
				for (var i = 0; i < block.blockLength; i++) {

					if (block.headingStartIndices && block.headingStartIndices.includes(i)) {
						rndContent += "";
						isHeading = true;
						currentHeadingType = block.headingTypes[i];
					} else if (block.lineBreakIndices && block.lineBreakIndices.includes(i)) {
						rndContent += isHeading ? "\n\n" : "\n";
						this.coloredBlocks.push({
							id: this.coloredBlocks.length,
							color: blockColor,
							content: rndContent,
							headingType: currentHeadingType,
						});
						sampleIndex = 0;
						isHeading = false;
						rndContent = "";
						currentHeadingType = "normal";

					} else {

						if (sampleIndex >= sampleText.length) {
							sampleIndex = 0;
						}
						rndContent += sampleText[sampleIndex];
						sampleIndex++;
					}
				}
				this.coloredBlocks.push({id: this.coloredBlocks.length, color: blockColor, content: rndContent, headingType: currentHeadingType});
			});
		},
		async sleep(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
		},
	},
};
</script>

<style scoped>
.scrollbox {
	visibility: hidden;
	position: absolute;
	float: left;
	width: 10%;
	height: 660px;
	top: 40px;
	bottom: 40px;
	font-size: 2px;
	overflow: auto;
	overflow-wrap: break-word;
	scrollbar-width: thin;
}

.scrollbox:hover {
	visibility: visible;
}

.scrollbox_delayed {
	transition: visibility 0.2s;
}

.scrollbox_delayed:hover {
	transition: visibility 0s 0.2s;
}

.minimap {
	background-color: rgba(0, 0, 0, 0.05);
	visibility: visible;
	min-height: 660px;
	user-select: none;
	overflow: auto;
	overflow-wrap: break-word;
}

.outerContainer {
	display: flex;
}

.scrollViewContainer {
	height: 100%;
	width: 100%;
	z-index: 10;
}

.scrollView {
	margin: 0;
	float: left;
	padding: 0;
	width: 100%;
	height: 90px;
	opacity: 0.5;
	position: absolute;
	transition: top 300ms ease-out;
}

.textBlockContainer {
	width: 100%;
	margin: 10% 10% 10% -75%;
}

.textBlock {
	white-space: pre-line;
}

.h1 {
	font-weight: 900;
	font-size: 6px;
}

.h2 {
	font-weight: 700;
	font-size: 4px;
}

.h3 {
	font-weight: 500;
	font-size: 3px;
}

.h4 {
	font-weight: 300;
	font-size: 2.5px;
}
</style>