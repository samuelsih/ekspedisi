import { Head } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from "zod";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { Check, ChevronsUpDown } from "lucide-react";
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from "@/components/ui/command";
import { useEffect, useState } from "react";
import axios from "axios";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Rating } from "@/components/ui/rating";
import Selector from "@/components/SelectSearch";

const formSchema = z.object({
    customerId: z.string({
        message: "ID Customer tidak boleh kosong"
    }),
    channelId: z.string({
        message: "Channel tidak boleh kosong",
    }),
    driverId: z.string({
        message: "Driver tidak boleh kosong",
    }),
    questions: z.record(z.number()),
});

type SurveySchema = z.infer<typeof formSchema>

interface Question {
    id: string;
    title: string;
}

interface Channel {
    id: string;
    name: string;
}

interface Props {
    title: string;
    subtitle: string;
    questions: Question[];
    channels: Channel[];
}

interface CustomerID {
    id: string;
    id_customer: string;
    name: string;
}

interface Driver {
    id: string;
    nik: string;
    name: string;
}

export default function Survey({ title, subtitle, questions, channels }: Props) {
    const form = useForm<SurveySchema>({ resolver: zodResolver(formSchema) })

    const [customerId, setCustomerId] = useState("")
    const [searchTermCustomerId, setSearchTermCustomerId] = useState("")
    const [openPopoverCustomerId, setOpenPopoverCustomerId] = useState(false)
    const [customerIds, setCustomerIds] = useState<CustomerID[]>([])

    useEffect(() => {
        const loadCustomerIds = async () => {
            const result = await axios.get<CustomerID[]>("/customer", { params: { search: searchTermCustomerId } })
            setCustomerIds(result.data)
        }

        loadCustomerIds()
    }, [searchTermCustomerId])

    const [answeredQuestions, setAnsweredQuestions] = useState<Record<string, number>>(
        questions.reduce<Record<string, number>>((acc, item) => {
            acc[item.id] = 0;
            return acc;
        }, {})
    )

    const [driverId, setDriverId] = useState("")

    const fetchDrivers = async (searchTerm: string) => {
        const result = await axios.get<Driver[]>("/driver", { params: { search: searchTerm } })
        return result.data
    }

    const handleSubmit = (_: SurveySchema) => {

    }

    return (
        <div>
            <Head title="Survey Ekspedisi JTA" />
            <h1 className="scroll-m-20 text-4xl font-extrabold tracking-tight lg:text-5xl text-center">
                {title}
            </h1>
            <h3 className="scroll-m-20 text-2xl font-semibold tracking-tight text-center">
                {subtitle}
            </h3>
            <Form {...form}>
                <form onSubmit={form.handleSubmit(handleSubmit)} className="space-y-8 max-w-3xl mx-auto py-10">
                <FormField
                    control={form.control}
                    name="customerId"
                    render={() => (
                        <FormItem>
                        <FormLabel>ID Customer</FormLabel>
                        <Popover open={openPopoverCustomerId} onOpenChange={setOpenPopoverCustomerId}>
                            <PopoverTrigger asChild>
                            <FormControl>
                                <Button
                                    variant="outline"
                                    role="combobox"
                                    className={cn(
                                        "w-full justify-between",
                                        !customerId && "text-muted-foreground"
                                    )}
                                    aria-expanded={openPopoverCustomerId}
                                >
                                {customerId
                                    ? customerIds.find(
                                        (customer) => customer.id === customerId
                                    )?.name
                                    : "Cari ID Customer"}
                                <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </FormControl>
                            </PopoverTrigger>
                            <PopoverContent className="w-[--radix-popover-trigger-width] p-0">
                            <Command>
                                <CommandInput
                                    className="w-full"
                                    placeholder="Cari ID Customer..."
                                    onValueChange={(value) => {
                                        setSearchTermCustomerId(value)
                                    }}
                                />
                                <CommandList>
                                <CommandEmpty>Tidak Ditemukan</CommandEmpty>
                                <CommandGroup>
                                    {customerIds.map((customer) => (
                                    <CommandItem
                                        value={customer.id}
                                        key={customer.id}
                                        onSelect={(value) => {
                                            const selectedCustomer = customerIds.find(customer => {
                                                return customer.id == value
                                            })

                                            if(selectedCustomer) {
                                                setCustomerId(value)
                                                setOpenPopoverCustomerId(false)
                                            }
                                        }}
                                    >
                                        <Check
                                            className={cn(
                                                "mr-2 h-4 w-4",
                                                customer.id === customerId
                                                ? "opacity-100"
                                                : "opacity-0"
                                            )}
                                        />
                                        {customer.id_customer} - {customer.name}
                                    </CommandItem>
                                    ))}
                                </CommandGroup>
                                </CommandList>
                            </Command>
                            </PopoverContent>
                        </Popover>
                        <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="channelId"
                    render={({ field }) => (
                        <FormItem>
                        <FormLabel>Channel</FormLabel>
                        <Select onValueChange={field.onChange} defaultValue={field.value}>
                            <FormControl>
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih Channel" />
                            </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                                {channels.map((channel) => (
                                    <SelectItem key={channel.id} value={channel.id}>{channel.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <FormMessage />
                        </FormItem>
                    )}
                />

                <FormField
                    control={form.control}
                    name="driverId"
                    render={() => (
                        <Selector
                            label="NIK Supir"
                            value={driverId}
                            searchKey="nik"
                            fetchItemsFn={fetchDrivers}
                            onChange={(v) => setDriverId(v)}
                            renderItem={(driver: Driver) => `${driver.nik} - ${driver.name}`}
                        />
                    )}
                />

                {questions.map(question => (
                    <FormField
                        key={question.id}
                        control={form.control}
                        name={"questions"}
                        render={({ field }) => (
                            <FormItem className="flex flex-col items-start">
                                <FormLabel>{question.title}</FormLabel>
                                <FormControl className="w-full">
                                    <Rating value={answeredQuestions[question.id]} onChange={(value) => {
                                        setAnsweredQuestions(qs => ({
                                            ...qs,
                                            [question.id]: value,
                                        }));
                                    }}/>
                                </FormControl>
                                <FormDescription>Nilai ({answeredQuestions[question.id]}/5)</FormDescription>
                                <FormMessage />
                            </FormItem>
                        )}
                    />
                ))}

                <Button type="submit" className="w-full">Kirim</Button>
                </form>
            </Form>
        </div>
    )
}
