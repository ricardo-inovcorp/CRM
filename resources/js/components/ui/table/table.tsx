import { defineComponent, h } from "vue"
import { cn } from "@/lib/utils"

const Table = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('table', { 
        class: cn("w-full caption-bottom text-sm") 
      }, slots.default?.())
  }
})

const TableHeader = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('thead', { 
        class: cn("[&_tr]:border-b") 
      }, slots.default?.())
  }
})

const TableBody = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('tbody', { 
        class: cn("[&_tr:last-child]:border-0") 
      }, slots.default?.())
  }
})

const TableFooter = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('tfoot', { 
        class: cn("bg-primary font-medium text-primary-foreground") 
      }, slots.default?.())
  }
})

const TableRow = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('tr', { 
        class: cn("border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted") 
      }, slots.default?.())
  }
})

const TableHead = defineComponent({
  setup(props, { slots, attrs }) {
    return () => 
      h('th', { 
        ...attrs,
        class: cn(
          "h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0",
          attrs.class
        ) 
      }, slots.default?.())
  }
})

const TableCell = defineComponent({
  setup(props, { slots, attrs }) {
    return () => 
      h('td', { 
        ...attrs,
        class: cn(
          "p-4 align-middle [&:has([role=checkbox])]:pr-0",
          attrs.class
        ) 
      }, slots.default?.())
  }
})

const TableCaption = defineComponent({
  setup(_, { slots }) {
    return () => 
      h('caption', { 
        class: cn("mt-4 text-sm text-muted-foreground") 
      }, slots.default?.())
  }
})

export {
  Table,
  TableHeader,
  TableBody,
  TableFooter,
  TableRow,
  TableHead,
  TableCell,
  TableCaption,
} 