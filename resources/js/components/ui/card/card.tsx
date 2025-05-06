import { defineComponent, h } from "vue"
import { cn } from "@/lib/utils"

export const Card = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("div", {
      ...attrs,
      class: cn(
        "rounded-lg border bg-card text-card-foreground shadow-sm",
        attrs.class
      )
    }, slots.default?.())
  }
})

export const CardHeader = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("div", {
      ...attrs,
      class: cn("flex flex-col space-y-1.5 p-6", attrs.class)
    }, slots.default?.())
  }
})

export const CardTitle = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("h3", {
      ...attrs,
      class: cn(
        "text-lg font-semibold leading-none tracking-tight",
        attrs.class
      )
    }, slots.default?.())
  }
})

export const CardDescription = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("p", {
      ...attrs,
      class: cn("text-sm text-muted-foreground", attrs.class)
    }, slots.default?.())
  }
})

export const CardContent = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("div", {
      ...attrs,
      class: cn("p-6 pt-0", attrs.class)
    }, slots.default?.())
  }
})

export const CardFooter = defineComponent({
  setup(_, { slots, attrs }) {
    return () => h("div", {
      ...attrs,
      class: cn("flex items-center p-6 pt-0", attrs.class)
    }, slots.default?.())
  }
}) 