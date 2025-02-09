import { Head } from "@inertiajs/react";
import { useForm } from "react-hook-form";
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from "zod";
import { Form, FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { Button } from "@/components/ui/button";
import axios from "axios";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Rating } from "@/components/ui/rating";
import Selector from "@/components/SelectSearch";

const formSchema = z.object({
    customerId: z.string({ message: "ID Customer tidak boleh kosong" }).nonempty("ID Customer tidak boleh kosong"),
    channelId: z.string({ message: "ID Channel tidak boleh kosong" }).nonempty("Channel tidak boleh kosong"),
    driverId: z.string({ message: "ID Driver tidak boleh kosong" }).nonempty("Driver tidak boleh kosong"),
    questions: z.record(
        z.number()
            .int("Rating harus bilangan bulat")
            .min(1, "Minimal rating adalah 1")
            .max(5, "Maksimal rating adalah 5")
    ).superRefine((data, ctx) => {
        Object.entries(data).forEach(([key, value]) => {
            if (!Number.isInteger(value)) {
                ctx.addIssue({
                    code: "custom",
                    message: `Rating harus bilangan bulat`,
                    path: ["questions", key],
                });
            } else if (value < 1 || value > 5) {
                ctx.addIssue({
                    code: "custom",
                    message: `Rating harus antara 1-5`,
                    path: ["questions", key],
                });
            }
        });
    }),
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
    const form = useForm<SurveySchema>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            questions: questions.reduce<Record<string, number>>((acc, item) => {
                acc[item.id] = 0;
                return acc;
            }, {})
        }
    })

    const fetchDrivers = async (searchTerm: string) => {
        const result = await axios.get<Driver[]>("/driver", { params: { search: searchTerm } })
        return result.data
    }

    const fetchCustomerIds = async (searchTerm: string) => {
        const result = await axios.get<CustomerID[]>("/customer", { params: { search: searchTerm } })
        return result.data
    }

    const handleSubmit = (schema: SurveySchema) => {
        console.log(schema.channelId, schema.customerId, schema.driverId, schema.questions)
    }

    return (
        <div className="container mx-auto p-8">
            <Head title="Survey" />
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
                    render={({ field }) => (
                        <Selector
                            searchKey="customer"
                            label="ID Customer"
                            value={field.value}
                            fetchItemsFn={fetchCustomerIds}
                            onChange={field.onChange}
                            renderDisplayOnFound={(items) => {
                                if(!field.value) {
                                    return "Cari ID Customer"
                                }

                                const selectedItem = items.find((item) => item.id === field.value);
                                if(!selectedItem) {
                                    return "Cari ID Customer"
                                }

                                return `${selectedItem.id_customer} (${selectedItem.name})`
                            }}
                            renderDropdownList={(customer: CustomerID) => `${customer.id_customer} (${customer.name})`}
                        />
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
                    render={({ field }) => (
                        <Selector
                            searchKey="driver"
                            label="NIK Supir"
                            value={field.value}
                            fetchItemsFn={fetchDrivers}
                            onChange={field.onChange}
                            renderDisplayOnFound={(items) => {
                                if(!field.value) {
                                    return "Cari NIK Supir"
                                }

                                const selectedItem = items.find((item) => item.id === field.value);
                                if(!selectedItem) {
                                    return "Cari NIK Supir"
                                }

                                return `${selectedItem.nik} (${selectedItem.name})`
                            }}
                            renderDropdownList={(driver: Driver) => `${driver.nik} (${driver.name})`}
                        />
                    )}
                />

                {questions.map(question => (
                    <FormField
                        key={question.id}
                        control={form.control}
                        name={`questions.${question.id}`}
                        render={({ field }) => (
                            <FormItem className="flex flex-col items-start">
                                <FormLabel>{question.title}</FormLabel>
                                <FormControl className="w-full">
                                    <Rating
                                        size="lg"
                                        value={field.value ?? 0}
                                        onChange={(value) => {
                                            form.setValue(`questions.${question.id}`, value, {
                                                shouldValidate: true,
                                            });
                                        }}
                                    />
                                </FormControl>
                                <FormDescription>Nilai ({field.value ?? 0}/5)</FormDescription>
                                <FormMessage>{form.formState.errors.questions?.[question.id]?.message}</FormMessage>
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
