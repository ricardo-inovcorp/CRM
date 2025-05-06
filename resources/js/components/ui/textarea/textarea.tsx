import { defineComponent, h } from "vue"
import { cn } from "@/lib/utils"

export const Textarea = defineComponent({
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "",
    },
    rows: {
      type: [String, Number],
      default: 3,
    },
  },
  emits: ["update:modelValue"],
  setup(props, { emit, attrs }) {
    return () => h("textarea", {
      value: props.modelValue,
      placeholder: props.placeholder,
      rows: props.rows,
      ...attrs,
      class: cn(
        "flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
        attrs.class
      ),
      onInput: (e: Event) => {
        emit("update:modelValue", (e.target as HTMLTextAreaElement).value)
      }
    })
  }
}) 