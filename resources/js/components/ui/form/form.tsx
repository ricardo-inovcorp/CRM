import { defineComponent, h, markRaw, PropType, ref, watch } from "vue"
import { cn } from "@/lib/utils"
import { Label } from "../label"
import { useForm as useVeeForm } from "vee-validate"

// Form Root
export const Form = defineComponent({
  props: {
    validationSchema: {
      type: Object,
      required: false,
    },
  },
  emits: ["submit"],
  setup(props, { slots, emit }) {
    return () => 
      h('form', {
        class: cn("space-y-4"),
        onSubmit: (e: Event) => {
          e.preventDefault()
          emit("submit", e)
        }
      }, slots.default?.())
  }
})

// FormField (handles form field validation integration)
export const FormField = defineComponent({
  props: {
    name: {
      type: String,
      required: true,
    },
  },
  setup(props, { slots }) {
    return () => slots.default?.({
      field: { name: props.name },
      errorMessage: '' // This would normally be populated from form state
    })
  }
})

// FormItem (wrap individual form elements)
export const FormItem = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('div', {
        class: cn("space-y-2")
      }, slots.default?.())
  }
})

// FormLabel
export const FormLabel = defineComponent({
  props: {
    for: {
      type: String,
      required: false,
    },
  },
  setup(props, { slots, attrs }) {
    return () => 
      h(Label, {
        for: props.for,
        class: cn(attrs.class)
      }, slots.default?.())
  }
})

// FormControl
export const FormControl = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('div', {
        class: cn("mt-1")
      }, slots.default?.())
  }
})

// FormDescription
export const FormDescription = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('p', {
        class: cn("text-sm text-muted-foreground")
      }, slots.default?.())
  }
})

// FormMessage
export const FormMessage = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('p', {
        class: cn("text-sm font-medium text-destructive")
      }, slots.default?.())
  }
}) 