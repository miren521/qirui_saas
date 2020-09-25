'use strict';
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else {
		factory(jQuery);
	}
}
(function ($) {
	var IS_TOUCH_DEVICE = ('ontouchstart' in document.documentElement);
	var DRAG_THRESHOLD = 5;
	var counter = 0;
	var dragEvents = (function () {
		if (IS_TOUCH_DEVICE) {
			return {
				START: 'touchstart',
				MOVE: 'touchmove',
				END: 'touchend'
			};
		} else {
			return {
				START: 'mousedown',
				MOVE: 'mousemove',
				END: 'mouseup'
			};
		}
	}());
	$.fn.arrangeable = function (options) {
		var dragging = false;
		var $clone;
		var dragElement;
		var originalClientX, originalClientY;
		var $elements;
		var touchDown = false;
		var leftOffset, topOffset;
		var eventNamespace;
		var $options = options;
		if (typeof options === "string") {
			if (options === 'destroy') {
				if (this.eq(0).data('drag-arrange-destroy')) {
					this.eq(0).data('drag-arrange-destroy')();
				}
				return this;
			}
		}
		options = $.extend({
			"dragEndEvent": "drag.end.arrangeable"
		}, options);
		var dragEndEvent = options["dragEndEvent"];
		$elements = this;
		eventNamespace = getEventNamespace();
		this.each(function () {
			var dragSelector = options.dragSelector;
			var self = this;
			var $this = $(this);
			if (dragSelector) {
				$this.on(dragEvents.START + eventNamespace,
					dragSelector, dragStartHandler);
			} else {
				$this.on(dragEvents.START + eventNamespace,
					dragStartHandler);
			}
			
			function dragStartHandler(e) {
				e.stopPropagation();
				touchDown = true;
				originalClientX = e.clientX
					|| e.originalEvent.touches[0].clientX;
				originalClientY = e.clientY
					|| e.originalEvent.touches[0].clientY;
				dragElement = self;
			}
		});
		$(document).on(dragEvents.MOVE + eventNamespace,
			dragMoveHandler).on(dragEvents.END + eventNamespace,
			dragEndHandler);
		
		function dragMoveHandler(e) {
			if (!touchDown) {
				return;
			}
			var $dragElement = $(dragElement);
			var dragDistanceX = (e.clientX || e.originalEvent.touches[0].clientX)
				- originalClientX;
			var dragDistanceY = (e.clientY || e.originalEvent.touches[0].clientY)
				- originalClientY;
			if (dragging) {
				e.stopPropagation();
				$clone.css({
					left: leftOffset + dragDistanceX,
					top: topOffset + dragDistanceY
				});
				shiftHoveredElement($clone, $dragElement, $elements);
			} else if (Math.abs(dragDistanceX) > DRAG_THRESHOLD
				|| Math.abs(dragDistanceY) > DRAG_THRESHOLD) {
				$clone = clone($dragElement);
				leftOffset = dragElement.offsetLeft
					- parseInt($dragElement.css('margin-left'))
					- parseInt($dragElement.css('padding-left'));
				topOffset = dragElement.offsetTop
					- parseInt($dragElement.css('margin-top'))
					- parseInt($dragElement.css('padding-top'));
				$clone.css({
					left: leftOffset,
					top: topOffset
				});
				$dragElement.parent().append($clone);
				$dragElement.css('visibility', 'hidden');
				dragging = true;
			}
		}
		
		function dragEndHandler(e) {
			if (dragging) {
				
				e.stopPropagation();
				dragging = false;
				$clone.remove();
				dragElement.style.visibility = 'visible';
				$(dragElement).parent().trigger(dragEndEvent,
					[$(dragElement)]);
				
				//拖拽结束后执行回调，传入当前拖拽的对象
				if ($options.callback) $options.callback($(dragElement));
			}
			touchDown = false;
			
		}
		
		function destroy() {
			$elements.each(function () {
				var dragSelector = options.dragSelector;
				var $this = $(this);
				if (dragSelector) {
					$this.off(dragEvents.START + eventNamespace,
						dragSelector);
				} else {
					$this.off(dragEvents.START + eventNamespace);
				}
			});
			$(document).off(dragEvents.MOVE + eventNamespace).off(
				dragEvents.END + eventNamespace);
			$elements.eq(0).data('drag-arrange-destroy', null);
			$elements = null;
			dragMoveHandler = null;
			dragEndHandler = null;
		}
		
		this.eq(0).data('drag-arrange-destroy', destroy);
		
		
	};
	
	function clone($element) {
		var $clone = $element.clone();
		$clone.css({
			position: 'absolute',
			width: $element.width(),
			height: $element.height(),
			'z-index': 100000
		});
		return $clone;
	}
	
	function getHoveredElement($clone, $dragElement, $movableElements) {
		var cloneOffset = $clone.offset();
		var cloneWidth = $clone.width();
		var cloneHeight = $clone.height();
		var cloneLeftPosition = cloneOffset.left;
		var cloneRightPosition = cloneOffset.left + cloneWidth;
		var cloneTopPosition = cloneOffset.top;
		var cloneBottomPosition = cloneOffset.top + cloneHeight;
		var $currentElement;
		var horizontalMidPosition, verticalMidPosition;
		var offset, overlappingX, overlappingY, inRange;
		for (var i = 0; i < $movableElements.length; i++) {
			$currentElement = $movableElements.eq(i);
			if ($currentElement[0] === $dragElement[0]) {
				continue;
			}
			offset = $currentElement.offset();
			horizontalMidPosition = offset.left + 0.5
				* $currentElement.width();
			verticalMidPosition = offset.top + 0.5
				* $currentElement.height();
			overlappingX = (horizontalMidPosition < cloneRightPosition)
				&& (horizontalMidPosition > cloneLeftPosition);
			overlappingY = (verticalMidPosition < cloneBottomPosition)
				&& (verticalMidPosition > cloneTopPosition);
			inRange = overlappingX && overlappingY;
			if (inRange) {
				
				return $currentElement[0];
			}
		}
		
		
	}
	
	function shiftHoveredElement($clone, $dragElement, $movableElements) {
		var hoveredElement = getHoveredElement($clone, $dragElement,
			$movableElements);
		if (hoveredElement !== $dragElement[0]) {
			var hoveredElementIndex = $movableElements
			.index(hoveredElement);
			var dragElementIndex = $movableElements.index($dragElement);
			if (hoveredElementIndex < dragElementIndex) {
				$(hoveredElement).before($dragElement);
			} else {
				$(hoveredElement).after($dragElement);
			}
			shiftElementPosition($movableElements, dragElementIndex,
				hoveredElementIndex);
		}
		
		
	}
	
	function shiftElementPosition(arr, fromIndex, toIndex) {
		var temp = arr.splice(fromIndex, 1)[0];
		
		return arr.splice(toIndex, 0, temp);
	}
	
	function getEventNamespace() {
		counter += 1;
		return '.drag-arrange-' + counter;
	}
}));