import { defineComponent, h, PropType } from "vue"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@/lib/utils"

const labelVariants = cva(
  "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
)

export type LabelVariants = VariantProps<typeof labelVariants>

export const Label = defineComponent({
  props: {
    for: {
      type: String,
      required: false,
    },
  },
  setup(props, { slots, attrs }) {
    return () => h("label", { 
      for: props.for,
      ...attrs,
      class: cn(labelVariants(), attrs.class)
    }, slots.default?.())
  }
}) 