import { defineComponent, h, PropType, ref, watch } from "vue"
import { cn } from "@/lib/utils"

// Main Select component (handles the state)
export const Select = defineComponent({
  props: {
    modelValue: {
      type: [String, Number],
      default: "",
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  emits: ["update:modelValue"],
  setup(props, { slots, emit }) {
    const open = ref(false)
    const value = ref(props.modelValue)
    
    watch(() => props.modelValue, (newVal) => {
      value.value = newVal
    })
    
    const toggleOpen = () => {
      if (!props.disabled) {
        open.value = !open.value
      }
    }
    
    const handleSelect = (val: string | number) => {
      value.value = val
      emit("update:modelValue", val)
      open.value = false
    }
    
    return () => h("div", {
      class: "relative w-full",
      "data-state": open.value ? "open" : "closed",
    }, slots.default?.({
      open: open.value,
      value: value.value,
      toggle: toggleOpen,
      select: handleSelect,
      disabled: props.disabled
    }))
  }
})

// Select Trigger (clickable part)
export const SelectTrigger = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("button", {
      type: "button",
      ...attrs,
      class: cn(
        "flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
        attrs.class
      ),
    }, slots.default?.())
  }
})

// Select Value
export const SelectValue = defineComponent({
  props: {
    placeholder: {
      type: String,
      default: "Select an option"
    }
  },
  setup(props) {
    return () => h("span", {
      class: "block truncate"
    }, props.placeholder)
  }
})

// Select Content (dropdown)
export const SelectContent = defineComponent({
  setup(_, { slots }) {
    return () => h("div", {
      class: "relative z-50 min-w-[8rem] overflow-hidden rounded-md border border-input bg-popover text-popover-foreground shadow-md animate-in fade-in-80"
    }, slots.default?.())
  }
})

// Select Item
export const SelectItem = defineComponent({
  props: {
    value: {
      type: [String, Number],
      required: true
    }
  },
  setup(props, { slots, attrs }) {
    return () => h("div", {
      ...attrs,
      class: cn(
        "relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
        attrs.class
      ),
    }, slots.default?.())
  }
}) 